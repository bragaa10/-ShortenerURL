<?php
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\ShortUrl $model */
/** @var array $campaigns */

$this->title = 'Editar: ' . ($model->title ?: $model->short_code);
$this->params['breadcrumbs'][] = ['label' => 'Links', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="page-header">
    <h1><i class="bi bi-pencil"></i> <?= Html::encode($this->title) ?></h1>
</div>

<?= $this->render('_form', ['model' => $model, 'campaigns' => $campaigns]) ?>
