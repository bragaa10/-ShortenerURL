<?php

/** @var yii\web\View $this */
/** @var app\models\ForgotPasswordForm $model */
/** @var string|null $resetLink */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Recuperar Password';
?>

<?php $form = ActiveForm::begin([
    'id' => 'forgot-password-form',
    'fieldConfig' => [
        'template' => "{label}\n{input}\n{error}",
        'labelOptions' => ['class' => 'form-label'],
        'inputOptions' => ['class' => 'form-control'],
        'errorOptions' => ['class' => 'help-block'],
    ],
]); ?>

<p style="color: var(--text-secondary); font-size: 14px; margin-bottom: 20px;">
    Introduza o seu email e receberá um link para redefinir a sua password.
</p>

<?= $form->field($model, 'email')->textInput(['autofocus' => true, 'placeholder' => 'seu@email.com']) ?>

<div class="form-group" style="margin-top: 24px;">
    <?= Html::submitButton('<i class="bi bi-envelope"></i> Enviar Link', ['class' => 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>

<?php if ($resetLink): ?>
<div class="alert alert-info" style="margin-top: 20px;">
    <strong><i class="bi bi-info-circle"></i> Modo de Desenvolvimento</strong><br>
    Link de redefinição gerado:<br>
    <a href="<?= Html::encode($resetLink) ?>" style="color: var(--accent-secondary); word-break: break-all;">
        <?= Html::encode($resetLink) ?>
    </a>
    <br><small style="opacity:.7;">Em produção, este link seria enviado por email.</small>
</div>
<?php endif; ?>

<div class="auth-links">
    <p><a href="<?= Yii::$app->urlManager->createUrl(['/site/login']) ?>">
        <i class="bi bi-arrow-left"></i> Voltar ao Login
    </a></p>
</div>
