<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Campaign $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="data-card">
    <div class="data-card-body">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => 'Ex: Black Friday 2026']) ?>

        <?= $form->field($model, 'description')->textarea(['rows' => 3, 'placeholder' => 'Campaign description (optional)']) ?>

        <?= $form->field($model, 'status')->dropDownList([1 => 'Active', 0 => 'Inactive']) ?>

        <div class="form-group" style="margin-top: 24px;">
            <?= Html::submitButton(
                $model->isNewRecord ? '<i class="bi bi-plus-lg"></i> Create Campaign' : '<i class="bi bi-check-lg"></i> Save',
                ['class' => 'btn btn-primary']
            ) ?>
            <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-secondary', 'style' => 'margin-left:8px;']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
