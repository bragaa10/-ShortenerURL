<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\ScanLogSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="scan-log-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'short_url_id') ?>

    <?= $form->field($model, 'accessed_at') ?>

    <?= $form->field($model, 'ip_address') ?>

    <?= $form->field($model, 'user_agent') ?>

    <?php // echo $form->field($model, 'referer') ?>

    <?php // echo $form->field($model, 'source') ?>

    <?php // echo $form->field($model, 'country') ?>

    <?php // echo $form->field($model, 'city') ?>

    <?php // echo $form->field($model, 'device_type') ?>

    <?php // echo $form->field($model, 'os') ?>

    <?php // echo $form->field($model, 'browser') ?>

    <?php // echo $form->field($model, 'language') ?>

    <?php // echo $form->field($model, 'utm_source') ?>

    <?php // echo $form->field($model, 'utm_medium') ?>

    <?php // echo $form->field($model, 'utm_campaign') ?>

    <?php // echo $form->field($model, 'utm_term') ?>

    <?php // echo $form->field($model, 'utm_content') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
