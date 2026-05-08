<?php

/** @var yii\web\View $this */
/** @var app\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Login';
?>

<?php $form = ActiveForm::begin([
    'id' => 'login-form',
    'fieldConfig' => [
        'template' => "{label}\n{input}\n{error}",
        'labelOptions' => ['class' => 'form-label'],
        'inputOptions' => ['class' => 'form-control'],
        'errorOptions' => ['class' => 'help-block'],
    ],
]); ?>

<?= $form->field($model, 'email')->textInput(['autofocus' => true, 'placeholder' => 'seu@email.com']) ?>

<?= $form->field($model, 'password')->passwordInput(['placeholder' => '••••••••']) ?>

<?= $form->field($model, 'rememberMe')->checkbox([
    'template' => "<div class='form-check'>{input} {label}</div>\n{error}",
    'labelOptions' => ['class' => 'form-check-label', 'style' => 'color: var(--text-secondary); font-size: 13px;'],
]) ?>

<div class="form-group" style="margin-top: 24px;">
    <?= Html::submitButton('<i class="bi bi-box-arrow-in-right"></i> Entrar', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
</div>

<?php ActiveForm::end(); ?>

<div class="auth-links">
    <p>Não tem conta? <a href="<?= Yii::$app->urlManager->createUrl(['/site/register']) ?>">Criar conta</a></p>
    <p style="margin-top: 8px;">
        <a href="<?= Yii::$app->urlManager->createUrl(['/site/forgot-password']) ?>" style="color: var(--text-muted); font-size: 13px;">
            <i class="bi bi-key"></i> Esqueci a minha password
        </a>
    </p>
</div>
