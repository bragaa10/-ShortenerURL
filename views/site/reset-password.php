<?php

/** @var yii\web\View $this */
/** @var app\models\ResetPasswordForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Reset Password';
?>

<?php $form = ActiveForm::begin([
    'id' => 'reset-password-form',
    'fieldConfig' => [
        'template' => "{label}\n{input}\n{error}",
        'labelOptions' => ['class' => 'form-label'],
        'inputOptions' => ['class' => 'form-control'],
        'errorOptions' => ['class' => 'help-block'],
    ],
]); ?>

<p style="color: var(--text-secondary); font-size: 14px; margin-bottom: 20px;">
    Choose a new password with at least 6 characters.
</p>

<?= $form->field($model, 'password')->passwordInput(['autofocus' => true, 'placeholder' => 'New password']) ?>

<?= $form->field($model, 'password_confirm')->passwordInput(['placeholder' => 'Confirm new password']) ?>

<div class="form-group" style="margin-top: 24px;">
    <?= Html::submitButton('<i class="bi bi-shield-check"></i> Reset Password', ['class' => 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>

<div class="auth-links">
    <p><a href="<?= Yii::$app->urlManager->createUrl(['/site/login']) ?>">
        <i class="bi bi-arrow-left"></i> Back to Login
    </a></p>
</div>
