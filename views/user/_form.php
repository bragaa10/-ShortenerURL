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

        <div class="form-row">
            <?= $form->field($model, 'username')->textInput(['maxlength' => true, 'placeholder' => 'Username']) ?>
            <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'placeholder' => 'your@email.com']) ?>
        </div>

        <div class="form-row">
            <?= $form->field($model, 'role')->dropDownList([
                User::ROLE_USER => 'User',
                User::ROLE_ADMIN => 'Admin',
            ]) ?>

            <?= $form->field($model, 'status')->dropDownList([
                User::STATUS_ACTIVE => 'Active',
                User::STATUS_INACTIVE => 'Inactive',
            ]) ?>
        </div>

        <?php if ($isCreate): ?>
        <!-- Password field — only on creation -->
        <div class="form-group">
            <label for="plain_password" class="control-label">
                Password <span style="color:var(--accent-danger)">*</span>
            </label>
            <input type="password" id="plain_password" name="plain_password"
                class="form-control" placeholder="Minimum 6 characters" autocomplete="new-password">
            <?php if ($model->hasErrors('password_hash')): ?>
                <div class="help-block"><?= Html::encode($model->getFirstError('password_hash')) ?></div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="form-group" style="margin-top: 24px;">
            <?= Html::submitButton(
                $isCreate
                    ? '<i class="bi bi-person-plus"></i> Create User'
                    : '<i class="bi bi-check-lg"></i> Save Changes',
                ['class' => 'btn btn-primary']
            ) ?>
            <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-secondary', 'style' => 'margin-left: 8px;']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
