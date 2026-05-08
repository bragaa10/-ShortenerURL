<?php

/** @var yii\web\View $this */
/** @var app\models\RegisterForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Criar Conta';
?>

<?php $form = ActiveForm::begin([
    'id' => 'register-form',
    'fieldConfig' => [
        'template' => "{label}\n{input}\n{error}",
        'labelOptions' => ['class' => 'form-label'],
        'inputOptions' => ['class' => 'form-control'],
        'errorOptions' => ['class' => 'help-block'],
    ],
]); ?>

<?= $form->field($model, 'username')->textInput(['placeholder' => 'O seu nome', 'autofocus' => true]) ?>

<?= $form->field($model, 'email')->textInput(['placeholder' => 'seu@email.com']) ?>

<?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Mínimo 6 caracteres']) ?>

<?= $form->field($model, 'password_confirm')->passwordInput(['placeholder' => 'Repetir password']) ?>

<div class="form-group" style="margin-top: 24px;">
    <?= Html::submitButton('<i class="bi bi-person-plus"></i> Criar Conta', ['class' => 'btn btn-primary', 'name' => 'register-button']) ?>
</div>

<?php ActiveForm::end(); ?>

<div class="auth-links">
    <p>Já tem conta? <a href="<?= Yii::$app->urlManager->createUrl(['/site/login']) ?>">Fazer login</a></p>
</div>
