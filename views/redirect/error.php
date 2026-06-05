<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var string $title */
/** @var string $message */

$this->title = $title;
?>

<div class="auth-body">
    <div class="auth-wrapper">
        <div class="auth-card text-center">
            <div class="auth-brand">
                <i class="bi bi-exclamation-triangle-fill" style="color: var(--accent-warning);"></i>
                <h1><?= Html::encode($title) ?></h1>
                <p><?= Html::encode($message) ?></p>
            </div>
            
            <div style="margin-top: 30px;">
                <?= Html::a('<i class="bi bi-house"></i> Back to Home', ['/site/index'], ['class' => 'btn btn-primary w-100']) ?>
            </div>
        </div>
        
        <div class="text-center" style="margin-top: 20px; color: var(--text-muted); font-size: 13px;">
            &copy; <?= date('Y') ?> <?= Html::encode(Yii::$app->name) ?> — Premium URL Shortener
        </div>
    </div>
</div>
