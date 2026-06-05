<?php

namespace app\controllers;

use Yii;
use app\models\ShortUrl;
use app\models\ScanLog;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * RedirectController handles the public short URL redirection.
 * This is the core of the URL shortener — it tracks access and redirects.
 */
class RedirectController extends Controller
{
    /**
     * No layout needed for redirects.
     */
    public $layout = false;

    /**
     * Disable CSRF only for the public redirect action (GET).
     * All POST actions (e.g. password form) retain CSRF protection.
     * {@inheritdoc}
     */
    public function beforeAction($action)
    {
        if ($action->id === 'go') {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    /**
     * Handles short URL redirection with full tracking.
     *
     * Flow:
     * 1. Find short_code in database
     * 2. Verify link status (active)
     * 3. Verify expiration
     * 4. Collect access data (IP, User-Agent, Referer, UTM, etc.)
     * 5. Parse device/browser/OS information
     * 6. Save scan event to database
     * 7. HTTP 302 redirect to original URL
     *
     * @param string $shortCode the short code from the URL
     * @return \yii\web\Response
     */
    public function actionGo($shortCode)
    {
        
        $shortUrl = ShortUrl::find()
            ->where(['short_code' => $shortCode])
            ->one();

        // 1. Link exists?
        if (!$shortUrl) {
            return $this->render('error', [
                'title' => 'Link not found',
                'message' => 'The link code you entered does not exist or has been removed.',
            ]);
        }

        // 2. Is Active?
        if ($shortUrl->status != ShortUrl::STATUS_ACTIVE) {
            return $this->render('error', [
                'title' => 'Inactive Link',
                'message' => 'This link has been deactivated by the owner.',
            ]);
        }

        // 3. Is Expired?
        if ($shortUrl->expires_at && $shortUrl->expires_at < time()) {
            return $this->render('error', [
                'title' => 'Expired Link',
                'message' => 'This link has expired and is no longer available.',
            ]);
        }

        // Password protection check
        if ($shortUrl->password_protected) {
            $sessionKey = 'pw_ok_' . $shortUrl->id;
            $error = null;

            // Check if already verified in this session
            if (!Yii::$app->session->get($sessionKey)) {
                // Handle POST password submission
                if (Yii::$app->request->isPost) {
                    // Validate CSRF for POST
                    $token = Yii::$app->request->post(Yii::$app->request->csrfParam);
                    if (!Yii::$app->request->validateCsrfToken()) {
                        $error = 'Invalid request. Please try again.';
                    } else {
                        $entered = Yii::$app->request->post('link_password', '');
                        if ($shortUrl->validateLinkPassword($entered)) {
                            Yii::$app->session->set($sessionKey, true);
                            // Fall through to tracking + redirect below
                        } else {
                            $error = 'Incorrect password. Please try again.';
                        }
                    }
                }

                // Show password form if not yet verified
                if (!Yii::$app->session->get($sessionKey)) {
                    $this->layout = false;
                    return $this->render('password', [
                        'shortCode' => $shortCode,
                        'error' => $error,
                    ]);
                }
            }
        }

        // Collect and save tracking data
        $this->trackAccess($shortUrl);

        // HTTP 302 redirect to original URL
        return $this->redirect($shortUrl->original_url, 302);
    }

    /**
     * Collects access data and saves a ScanLog entry.
     *
     * @param ShortUrl $shortUrl
     */
    protected function trackAccess(ShortUrl $shortUrl)
    {
        $request = Yii::$app->request;
        $userAgent = $request->userAgent ?? '';
        $now = time();

        // Parse device information
        $deviceInfo = $this->parseUserAgent($userAgent);

        // Extract UTM parameters
        $utmSource = $request->get('utm_source');
        $utmMedium = $request->get('utm_medium');
        $utmCampaign = $request->get('utm_campaign');
        $utmTerm = $request->get('utm_term');
        $utmContent = $request->get('utm_content');

        // Determine access source
        $sourceParam = $request->get('source');
        $source = 'direct'; // Default

        if ($sourceParam === 'qr') {
            $source = 'qr';
        } elseif (!empty($utmSource)) {
            $source = 'utm';
        } elseif (!empty($request->referrer)) {
            $source = 'referral';
        }

        // Get IP address
        $ipAddress = $request->userIP;

        // Get geo information (async-friendly, non-blocking)
        $geoInfo = $this->getGeoInfo($ipAddress);

        // Create scan log entry
        $scanLog = new ScanLog();
        $scanLog->short_url_id = $shortUrl->id;
        $scanLog->accessed_at = $now;
        $scanLog->ip_address = $ipAddress;

        // Skip logging if it's a bot
        if (($deviceInfo['device_type'] ?? '') === 'bot') {
            return;
        }

        $scanLog->user_agent = mb_substr($userAgent, 0, 500);
        $scanLog->referer = $request->referrer ? mb_substr($request->referrer, 0, 500) : null;
        $scanLog->source = $source;
        $scanLog->country = $geoInfo['country'] ?? null;
        $scanLog->country_code = $geoInfo['country_code'] ?? null;
        $scanLog->city = $geoInfo['city'] ?? null;
        $scanLog->device_type = $deviceInfo['device_type'] ?? null;
        $scanLog->os = $deviceInfo['os'] ?? null;
        $scanLog->browser = $deviceInfo['browser'] ?? null;
        $scanLog->language = $this->parseLanguage($request->headers->get('Accept-Language'));
        $scanLog->utm_source = $utmSource;
        $scanLog->utm_medium = $utmMedium;
        $scanLog->utm_campaign = $utmCampaign;
        $scanLog->utm_term = $utmTerm;
        $scanLog->utm_content = $utmContent;
        $scanLog->created_at = $now;

        // Save without validation for performance (data is already sanitized)
        $scanLog->save(false);
    }

    /**
     * Parses User-Agent string to extract device, OS, and browser info.
     * Uses matomo/device-detector if available, falls back to basic parsing.
     *
     * @param string $userAgent
     * @return array ['device_type', 'os', 'browser']
     */
    protected function parseUserAgent($userAgent)
    {
        // Try matomo/device-detector first
        if (class_exists('\DeviceDetector\DeviceDetector')) {
            $dd = new \DeviceDetector\DeviceDetector($userAgent);
            $dd->parse();

            $deviceType = 'desktop';
            if ($dd->isMobile()) {
                $deviceType = 'mobile';
            } elseif ($dd->isTablet()) {
                $deviceType = 'tablet';
            } elseif ($dd->isBot()) {
                $deviceType = 'bot';
            }
            return [
                'device_type' => $deviceType,
                'os' => ($osInfo['name'] ?? 'Unknown') . ' ' . ($osInfo['version'] ?? ''),
                'browser' => ($clientInfo['name'] ?? 'Unknown') . ' ' . ($clientInfo['version'] ?? ''),
            ];
        }

        // Fallback: basic User-Agent parsing
        return $this->basicUserAgentParse($userAgent);
    }

    /**
     * Basic User-Agent parsing without external dependencies.
     *
     * @param string $userAgent
     * @return array
     */
    protected function basicUserAgentParse($userAgent)
    {
        $ua = strtolower($userAgent);

        // Device type
        $device_type = 'desktop';
        if (preg_match('/(mobile|android|iphone|ipod|phone|webos|blackberry)/i', $ua)) {
            $device_type = 'mobile';
        } elseif (preg_match('/(tablet|ipad|playbook|silk)/i', $ua)) {
            $device_type = 'tablet';
        } elseif (preg_match('/(bot|crawl|spider|slurp|mediapartners|adsbot|whatsapp|facebookexternalhit|linkedinbot|bingpreview|twitterbot|slackbot|discordbot)/i', $ua)) {
            $device_type = 'bot';
        }

        // OS
        $os = 'Unknown';
        if (strpos($ua, 'windows') !== false) $os = 'Windows';
        elseif (strpos($ua, 'macintosh') !== false || strpos($ua, 'mac os') !== false) $os = 'macOS';
        elseif (strpos($ua, 'linux') !== false) $os = 'Linux';
        elseif (strpos($ua, 'android') !== false) $os = 'Android';
        elseif (strpos($ua, 'iphone') !== false || strpos($ua, 'ipad') !== false) $os = 'iOS';

        // Browser
        $browser = 'Unknown';
        if (strpos($ua, 'edg/') !== false) $browser = 'Edge';
        elseif (strpos($ua, 'opr/') !== false || strpos($ua, 'opera') !== false) $browser = 'Opera';
        elseif (strpos($ua, 'chrome') !== false) $browser = 'Chrome';
        elseif (strpos($ua, 'firefox') !== false) $browser = 'Firefox';
        elseif (strpos($ua, 'safari') !== false) $browser = 'Safari';
        elseif (strpos($ua, 'msie') !== false || strpos($ua, 'trident') !== false) $browser = 'Internet Explorer';

        return compact('device_type', 'os', 'browser');
    }

    /**
     * Gets geo information from IP address using ipapi.co free API.
     *
     * @param string|null $ipAddress
     * @return array ['country' => string, 'city' => string]
     */
    protected function getGeoInfo($ipAddress)
    {
        if (empty($ipAddress) || in_array($ipAddress, ['127.0.0.1', '::1'])) {
            return ['country' => 'Local', 'city' => 'Localhost'];
        }

        // Try ipapi.co first
        $data = $this->fetchGeoData("https://ipapi.co/{$ipAddress}/json/");
        if ($data && !isset($data['error'])) {
            return [
                'country' => $data['country_name'] ?? null,
                'country_code' => $data['country_code'] ?? null,
                'city' => $data['city'] ?? null,
            ];
        }

        // Fallback to ip-api.com
        $data = $this->fetchGeoData("http://ip-api.com/json/{$ipAddress}");
        if ($data && isset($data['status']) && $data['status'] === 'success') {
            return [
                'country' => $data['country'] ?? null,
                'country_code' => $data['countryCode'] ?? null,
                'city' => $data['city'] ?? null,
            ];
        }

        return ['country' => null, 'city' => null];
    }

    /**
     * Helper to fetch JSON data using cURL (more reliable than file_get_contents)
     */
    private function fetchGeoData($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        curl_setopt($ch, CURLOPT_USERAGENT, 'EncurtadorURLs/1.0');
        
        // Disable SSL verification for compatibility if needed (use with caution in strict envs)
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response ? json_decode($response, true) : null;
    }

    /**
     * Parses Accept-Language header to get primary language.
     *
     * @param string|null $header
     * @return string|null e.g., "pt-PT", "en-US"
     */
    protected function parseLanguage($header)
    {
        if (empty($header)) {
            return null;
        }

        // Get first language from Accept-Language header
        $languages = explode(',', $header);
        $primary = trim($languages[0]);

        // Remove quality value (e.g., ";q=0.9")
        $parts = explode(';', $primary);
        return mb_substr(trim($parts[0]), 0, 20);
    }
}
