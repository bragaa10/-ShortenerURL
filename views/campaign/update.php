<?php
use yii\helpers\Html;
$this->title = 'Editar: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Campanhas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-header">
    <h1><i class="bi bi-pencil"></i> <?= Html::encode($this->title) ?></h1>
</div>
<?= $this->render('_form', ['model' => $model]) ?>
