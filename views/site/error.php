<?php

/** @var yii\web\View $this */
/** @var string $name */
/** @var string $message */
/** @var Exception $exception */

use yii\helpers\Html;

$this->title = $name;
?>
<div class="site-error" style="padding: 80px 20px; text-align: center;">
    <h1><?= Html::encode($this->title) ?></h1>
    <p style="color: var(--text-secondary); font-size: 18px; margin-top: 16px;">
        <?= nl2br(Html::encode($message)) ?>
    </p>
    <div style="margin-top: 32px;">
        <a href="<?= Yii::$app->homeUrl ?>" class="btn btn-primary">
            <i class="bi bi-house"></i> Página Inicial
        </a>
    </div>
</div>
