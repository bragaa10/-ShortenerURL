<?php
use yii\helpers\Html;
$this->title = 'Nova Campanha';
$this->params['breadcrumbs'][] = ['label' => 'Campanhas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-header">
    <h1><i class="bi bi-plus-lg"></i> <?= Html::encode($this->title) ?></h1>
</div>
<?= $this->render('_form', ['model' => $model]) ?>
