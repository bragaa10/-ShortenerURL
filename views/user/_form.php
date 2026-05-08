<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\User;

/** @var yii\web\View $this */
/** @var app\models\User $model */
/** @var yii\widgets\ActiveForm $form */
/** @var bool $isCreate */
$isCreate = $isCreate ?? $model->isNewRecord;
?>

<div class="data-card">
    <div class="data-card-body">

        <?php $form = ActiveForm::begin(); ?>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <?= $form->field($model, 'username')->textInput(['maxlength' => true, 'placeholder' => 'Nome do utilizador']) ?>
            <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'placeholder' => 'seu@email.com']) ?>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <?= $form->field($model, 'role')->dropDownList([
                User::ROLE_USER => 'Cliente',
                User::ROLE_ADMIN => 'Admin',
            ]) ?>

            <?= $form->field($model, 'status')->dropDownList([
                User::STATUS_ACTIVE => 'Ativo',
                User::STATUS_INACTIVE => 'Inativo',
            ]) ?>
        </div>

        <?php if ($isCreate): ?>
        <!-- Password field — only on creation -->
        <div class="form-group">
            <label for="plain_password" class="control-label">
                Password <span style="color:var(--accent-danger)">*</span>
            </label>
            <input type="password" id="plain_password" name="plain_password"
                class="form-control" placeholder="Mínimo 6 caracteres" autocomplete="new-password">
            <?php if ($model->hasErrors('password_hash')): ?>
                <div class="help-block"><?= Html::encode($model->getFirstError('password_hash')) ?></div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="form-group" style="margin-top: 24px;">
            <?= Html::submitButton(
                $isCreate
                    ? '<i class="bi bi-person-plus"></i> Criar Utilizador'
                    : '<i class="bi bi-check-lg"></i> Guardar Alterações',
                ['class' => 'btn btn-primary']
            ) ?>
            <?= Html::a('Cancelar', ['index'], ['class' => 'btn btn-secondary', 'style' => 'margin-left: 8px;']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
