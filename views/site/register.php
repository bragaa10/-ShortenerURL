<?php

/** @var yii\web\View $this */
/** @var app\models\RegisterForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Create Account';
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

<?= $form->field($model, 'username')->textInput(['placeholder' => 'Your name', 'autofocus' => true]) ?>

<?= $form->field($model, 'email')->textInput(['placeholder' => 'your@email.com']) ?>

<?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Minimum 6 characters']) ?>

<?= $form->field($model, 'password_confirm')->passwordInput(['placeholder' => 'Repeat password']) ?>

<div class="form-group" style="margin-top: 24px;">
    <?= Html::submitButton('<i class="bi bi-person-plus"></i> Create Account', ['class' => 'btn btn-primary', 'name' => 'register-button']) ?>
</div>

<?php ActiveForm::end(); ?>

<div class="auth-links">
    <p>Already have an account? <a href="<?= Yii::$app->urlManager->createUrl(['/site/login']) ?>">Login</a></p>
</div>
