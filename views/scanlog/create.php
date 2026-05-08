<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\ScanLog $model */

$this->title = 'Create Scan Log';
$this->params['breadcrumbs'][] = ['label' => 'Scan Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="scan-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
