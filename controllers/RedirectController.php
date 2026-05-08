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
     * Disable CSRF for GET redirect action (public).
     * CSRF is still validated for POST (password form).
     * {@inheritdoc}
     */
    public $enableCsrfValidation = false;

    /**
     * No layout needed for redirects.
     */
    public $layout = false;

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

        // Link not found
        if ($shortUrl === null) {
            throw new NotFoundHttpException('O link solicitado não existe.');
        }

        // Link inactive
        if ($shortUrl->status != ShortUrl::STATUS_ACTIVE) {
            return $this->render('expired', [
                'reason' => 'inactive',
                'message' => 'Este link está desativado.',
            ]);
        }

        // Link expired
        if ($shortUrl->isExpired()) {
            return $this->render('expired', [
                'reason' => 'expired',
                'message' => 'Este link expirou.',
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
                        $error = 'Pedido inválido. Tente novamente.';
                    } else {
                        $entered = Yii::$app->request->post('link_password', '');
                        if ($shortUrl->validateLinkPassword($entered)) {
                            Yii::$app->session->set($sessionKey, true);
                            // Fall through to tracking + redirect below
                        } else {
                            $error = 'Password incorreta. Tente novamente.';
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
        $source = $request->get('source', 'direct'); // Default to direct
        if (!empty($utmSource)) {
            $source = 'utm';
        } elseif (!empty($request->referrer)) {
            if ($source === 'direct') { // Don't override 'qr' if it was set via param
                $source = 'referral';
            }
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
        $scanLog->user_agent = mb_substr($userAgent, 0, 500);
        $scanLog->referer = $request->referrer ? mb_substr($request->referrer, 0, 500) : null;
        $scanLog->source = $source;
        $scanLog->country = $geoInfo['country'] ?? null;
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

            $client = $dd->getClient();
            $os = $dd->getOs();

            return [
                'device_type' => $deviceType,
                'os' => is_array($os) ? ($os['name'] ?? 'Unknown') : 'Unknown',
                'browser' => is_array($client) ? ($client['name'] ?? 'Unknown') : 'Unknown',
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
        } elseif (preg_match('/(bot|crawl|spider|slurp|mediapartners)/i', $ua)) {
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

        try {
            $url = "https://ipapi.co/{$ipAddress}/json/";
            $context = stream_context_create([
                'http' => [
                    'timeout' => 2, // 2 second timeout to not block redirect
                    'header' => "User-Agent: EncurtadorURLs/1.0\r\n",
                ],
            ]);

            $response = @file_get_contents($url, false, $context);
            if ($response !== false) {
                $data = json_decode($response, true);
                if ($data && !isset($data['error'])) {
                    return [
                        'country' => $data['country_name'] ?? null,
                        'city' => $data['city'] ?? null,
                    ];
                }
            }
        } catch (\Exception $e) {
            Yii::warning("GeoIP lookup failed for {$ipAddress}: " . $e->getMessage(), __METHOD__);
        }

        return ['country' => null, 'city' => null];
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
