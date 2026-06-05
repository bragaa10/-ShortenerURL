<?php
use yii\helpers\Html;

/** @var yii\web\View $this */

$this->title = Yii::$app->name;
?>
<div class="site-index">
    <div class="jumbotron text-center bg-transparent mt-5 mb-5" style="padding: 60px 0;">
        <h1 class="display-4" style="font-weight: 700; color: var(--text-primary);">Simplify Your Links</h1>
        <p class="lead" style="color: var(--text-secondary); max-width: 600px; margin: 20px auto;">
            The ultimate URL shortener for professional branding and precise tracking. 
            Create short links, generate QR codes, and analyze your traffic in real-time.
        </p>
        
        <div class="mt-4">
            <?php if (Yii::$app->user->isGuest): ?>
                <?= Html::a('Get Started for Free', ['/site/register'], ['class' => 'btn btn-lg btn-primary', 'style' => 'padding: 12px 32px; border-radius: 30px;']) ?>
                <div class="mt-3">
                    <span style="color: var(--text-muted); font-size: 14px;">Already have an account?</span>
                    <?= Html::a('Login', ['/site/login'], ['style' => 'color: var(--accent-primary); font-weight: 500; margin-left: 5px;']) ?>
                </div>
            <?php else: ?>
                <?= Html::a('Go to Dashboard', ['/dashboard/index'], ['class' => 'btn btn-lg btn-primary', 'style' => 'padding: 12px 32px; border-radius: 30px;']) ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="body-content">
        <div class="grid-3" style="margin-top: 40px;">
            <div class="data-card" style="text-align: center; padding: 32px;">
                <div class="stat-icon primary" style="margin: 0 auto 20px;"><i class="bi bi-lightning-charge-fill"></i></div>
                <h3 style="margin-bottom: 15px;">Fast & Reliable</h3>
                <p style="color: var(--text-secondary); font-size: 14px; line-height: 1.6;">
                    Instant redirects and high availability. Your links will always work when your customers click them.
                </p>
            </div>

            <div class="data-card" style="text-align: center; padding: 32px;">
                <div class="stat-icon success" style="margin: 0 auto 20px;"><i class="bi bi-qr-code"></i></div>
                <h3 style="margin-bottom: 15px;">QR Code Ready</h3>
                <p style="color: var(--text-secondary); font-size: 14px; line-height: 1.6;">
                    Every link automatically gets a high-quality QR code. Perfect for print materials and offline marketing.
                </p>
            </div>

            <div class="data-card" style="text-align: center; padding: 32px;">
                <div class="stat-icon secondary" style="margin: 0 auto 20px;"><i class="bi bi-bar-chart-line-fill"></i></div>
                <h3 style="margin-bottom: 15px;">Detailed Analytics</h3>
                <p style="color: var(--text-secondary); font-size: 14px; line-height: 1.6;">
                    Track clicks, location, devices, and browsers. Understand exactly who is engaging with your content.
                </p>
            </div>
        </div>
    </div>
</div>
