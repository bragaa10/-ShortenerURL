<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\ScanLog $model */

$this->title = 'Update Scan Log: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Scan Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="scan-log-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
