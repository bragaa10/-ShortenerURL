<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\User $model */

$this->title = 'Novo Utilizador';
$this->params['breadcrumbs'][] = ['label' => 'Utilizadores', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="page-header">
    <h1><i class="bi bi-person-plus-fill"></i> Novo Utilizador</h1>
    <?= Html::a('<i class="bi bi-arrow-left"></i> Voltar', ['index'], ['class' => 'btn btn-secondary']) ?>
</div>

<?= $this->render('_form', ['model' => $model, 'isCreate' => true]) ?>
