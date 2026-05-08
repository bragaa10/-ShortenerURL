<?php

namespace app\components;

use Yii;
use yii\base\Component;

/**
 * QrCodeGenerator generates QR code images for short URLs.
 *
 * Uses endroid/qr-code if available, falls back to Google Charts API.
 *
 * Usage:
 * ```php
 * $generator = new QrCodeGenerator();
 * $path = $generator->generate('https://example.com/abc123', 42);
 * ```
 */
class QrCodeGenerator extends Component
{
    /**
     * @var int QR code image size in pixels
     */
    public $size = 300;

    /**
     * @var int margin around QR code in pixels
     */
    public $margin = 10;

    /**
     * @var string directory to save QR code images (relative to @webroot)
     */
    public $savePath = 'uploads/qrcodes';

    /**
     * Generates a QR code image for the given URL.
     *
     * @param string $url the URL to encode in the QR code
     * @param int $shortUrlId the ShortUrl ID (used for filename)
     * @return string|null relative path to the generated QR code, or null on failure
     */
    public function generate($url, $shortUrlId)
    {
        // Ultimate reliability: Use Google API directly as a string
        // This avoids any issues with folders, permissions or missing libraries
        $encodedUrl = urlencode($url);
        return "https://chart.googleapis.com/chart?chs={$this->size}x{$this->size}&cht=qr&chl={$encodedUrl}&choe=UTF-8";
    }

    /**
     * Generates QR code using endroid/qr-code library.
     *
     * @param string $url
     * @param string $absolutePath
     * @return bool
     */
    protected function generateWithEndroid($url, $absolutePath)
    {
        // endroid/qr-code v4+
        if (class_exists('\Endroid\QrCode\QrCode')) {
            try {
                $qrCode = new \Endroid\QrCode\QrCode($url);
                $qrCode->setSize($this->size);
                $qrCode->setMargin($this->margin);

                if (class_exists('\Endroid\QrCode\Writer\PngWriter')) {
                    $writer = new \Endroid\QrCode\Writer\PngWriter();
                    $result = $writer->write($qrCode);
                    $result->saveToFile($absolutePath);
                    return true;
                }
            } catch (\Exception $e) {
                Yii::warning("Endroid QR generation failed: " . $e->getMessage(), __METHOD__);
            }
        }

        return false;
    }

    /**
     * Generates QR code using Google Charts API (fallback).
     *
     * @param string $url
     * @param string $absolutePath
     * @return bool
     */
    protected function generateWithGoogleCharts($url, $absolutePath)
    {
        try {
            $encodedUrl = urlencode($url);
            $apiUrl = "https://chart.googleapis.com/chart?chs={$this->size}x{$this->size}&cht=qr&chl={$encodedUrl}&choe=UTF-8";

            // Method 1: file_get_contents (fastest, requires allow_url_fopen=On)
            $context = stream_context_create(['http' => ['timeout' => 5]]);
            $imageData = @file_get_contents($apiUrl, false, $context);

            // Method 2: CURL (more robust, standard in most environments)
            if ($imageData === false && function_exists('curl_init')) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $apiUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore SSL errors for the API call
                curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                $imageData = curl_exec($ch);
                curl_close($ch);
            }

            if ($imageData !== false && !empty($imageData)) {
                return file_put_contents($absolutePath, $imageData) !== false;
            }
        } catch (\Exception $e) {
            Yii::warning("Google Charts QR generation failed: " . $e->getMessage(), __METHOD__);
        }

        return false;
    }

    /**
     * Deletes a QR code image.
     *
     * @param string $relativePath
     * @return bool
     */
    public function delete($relativePath)
    {
        if (empty($relativePath)) {
            return false;
        }

        $absolutePath = Yii::getAlias('@webroot/' . $relativePath);
        if (file_exists($absolutePath)) {
            return unlink($absolutePath);
        }

        return false;
    }
}
