<?php

/** @var yii\web\View $this */

use yii\helpers\Html;

$this->title = 'Privacy Policy';
?>

<div class="page-header">
    <h1><i class="bi bi-shield-check"></i> Privacy Policy</h1>
</div>

<div class="data-card">
    <div class="data-card-header">
        <h3>Url Shortener — Privacy Policy and GDPR</h3>
        <span style="color: var(--text-muted); font-size: 13px;">Last updated: <?= date('d/m/Y') ?></span>
    </div>
    <div class="data-card-body" style="line-height: 1.8; color: var(--text-secondary);">

        <h4 style="color: var(--text-primary); margin: 24px 0 12px;">1. Data Collected</h4>
        <p>When using this platform, we collect the following data for access tracking purposes for shortened links:</p>
        <ul style="padding-left: 24px; margin-bottom: 16px;">
            <li>IP Address (anonymized after 90 days)</li>
            <li>Date and time of access</li>
            <li>Browser User-Agent</li>
            <li>Country and city (obtained by IP geolocation)</li>
            <li>Device type, operating system, and browser</li>
            <li>Source URL (Referer)</li>
            <li>UTM parameters (if present in the link)</li>
            <li>Browser language</li>
        </ul>

        <h4 style="color: var(--text-primary); margin: 24px 0 12px;">2. Purpose of Processing</h4>
        <p>The collected data is used exclusively for:</p>
        <ul style="padding-left: 24px; margin-bottom: 16px;">
            <li>Providing statistics to the link creator (how many accesses, from where, etc.)</li>
            <li>Detecting abuse and malicious links</li>
            <li>Improving platform performance</li>
        </ul>

        <h4 style="color: var(--text-primary); margin: 24px 0 12px;">3. Data Retention</h4>
        <p>Access logs (scan logs) are kept for a maximum period of <strong>365 days</strong>.
        IP data is anonymized after <strong>90 days</strong>. Users can request the deletion
        of their data at any time.</p>

        <h4 style="color: var(--text-primary); margin: 24px 0 12px;">4. Your Rights (GDPR)</h4>
        <p>Under the General Data Protection Regulation (GDPR), you have the following rights:</p>
        <ul style="padding-left: 24px; margin-bottom: 16px;">
            <li><strong>Right of Access</strong> — Consult all data we have about you</li>
            <li><strong>Right to Rectification</strong> — Correct incorrect data</li>
            <li><strong>Right to Erasure</strong> — Request deletion of your account and data</li>
            <li><strong>Right to Object</strong> — Object to the processing of your data</li>
            <li><strong>Right to Portability</strong> — Export your data in CSV format</li>
        </ul>

        <h4 style="color: var(--text-primary); margin: 24px 0 12px;">5. Cookies</h4>
        <p>We use essential technical cookies for the platform's operation (session, CSRF protection).
        We do not use third-party tracking cookies.</p>

        <h4 style="color: var(--text-primary); margin: 24px 0 12px;">6. Data Sharing</h4>
        <p>Your data <strong>is not shared with third parties</strong>, except for IP geolocation
        (external service ipapi.co, subject to its own privacy policy). Geolocation is performed
        at the time of access and only the result (country/city) is stored.</p>

        <h4 style="color: var(--text-primary); margin: 24px 0 12px;">7. Security</h4>
        <p>All passwords are stored with bcrypt hash. Communications are carried out over HTTPS.
        Access to data is restricted by authentication and access profile control.</p>

        <h4 style="color: var(--text-primary); margin: 24px 0 12px;">8. Contact</h4>
        <p>To exercise your rights or clarify doubts about privacy, contact the administrator
        of this platform.</p>

        <div style="margin-top: 32px; padding: 16px; background: rgba(16,185,129,0.08);
            border-left: 4px solid var(--accent-success); border-radius: 8px;">
            <p style="margin: 0; color: var(--accent-success); font-size: 14px;">
                <i class="bi bi-shield-fill-check"></i>
                <strong>GDPR Compliance:</strong> This platform was developed with privacy by design,
                following the principles of Regulation (EU) 2016/679.
            </p>
        </div>

    </div>
</div>
