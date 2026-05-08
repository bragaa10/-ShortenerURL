<?php

use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\UserSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Utilizadores';
?>

<div class="page-header">
    <h1><i class="bi bi-people-fill"></i> Utilizadores</h1>
    <?= yii\helpers\Html::a('<i class="bi bi-person-plus"></i> Novo Utilizador', ['create'], ['class' => 'btn btn-primary']) ?>
</div>

<div class="data-card">
    <div class="data-card-body" style="padding: 0;">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'tableOptions' => ['class' => 'table'],
            'columns' => [
                'username',
                'email:email',
                [
                    'attribute' => 'role',
                    'value' => function ($model) {
                        $class = $model->role === 'admin' ? 'badge-warning' : 'badge-info';
                        return '<span class="badge ' . $class . '">' . $model->getRoleLabel() . '</span>';
                    },
                    'format' => 'raw',
                    'filter' => ['user' => 'Cliente', 'admin' => 'Admin'],
                ],
                [
                    'attribute' => 'status',
                    'value' => function ($model) {
                        $class = $model->status == 10 ? 'badge-success' : 'badge-danger';
                        return '<span class="badge ' . $class . '">' . $model->getStatusLabel() . '</span>';
                    },
                    'format' => 'raw',
                    'filter' => [10 => 'Ativo', 0 => 'Inativo'],
                ],
                [
                    'attribute' => 'created_at',
                    'value' => function ($model) { return date('d/m/Y', $model->created_at); },
                    'filter' => false,
                ],
                [
                    'attribute' => 'last_login_at',
                    'label' => 'Último Login',
                    'value' => function ($model) {
                        return $model->last_login_at ? date('d/m/Y H:i', $model->last_login_at) : '—';
                    },
                    'filter' => false,
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view} {update} {toggle}',
                    'buttons' => [
                        'view' => function ($url, $model) {
                            return yii\helpers\Html::a('<i class="bi bi-eye"></i>', ['view', 'id' => $model->id], ['class' => 'btn btn-sm btn-secondary', 'style' => 'margin-right:4px;']);
                        },
                        'update' => function ($url, $model) {
                            return yii\helpers\Html::a('<i class="bi bi-pencil"></i>', ['update', 'id' => $model->id], ['class' => 'btn btn-sm btn-secondary', 'style' => 'margin-right:4px;']);
                        },
                        'toggle' => function ($url, $model) {
                            if ($model->id === Yii::$app->user->id) return '';
                            $icon = $model->status == 10 ? '<i class="bi bi-person-slash"></i>' : '<i class="bi bi-person-check"></i>';
                            $class = $model->status == 10 ? 'btn btn-sm btn-danger' : 'btn btn-sm btn-success';
                            return yii\helpers\Html::a($icon, ['toggle-status', 'id' => $model->id], [
                                'class' => $class,
                                'data-method' => 'post',
                                'title' => $model->status == 10 ? 'Desativar' : 'Ativar',
                            ]);
                        },
                    ],
                ],
            ],
        ]); ?>
    </div>
</div>
