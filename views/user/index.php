<?php

use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\UserSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Users';
?>

<div class="page-header">
    <h1><i class="bi bi-people-fill"></i> Users</h1>
    <?= yii\helpers\Html::a('<i class="bi bi-person-plus"></i> New User', ['create'], ['class' => 'btn btn-primary']) ?>
</div>

<div class="data-card">
    <div class="data-card-body" style="padding: 0;">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'tableOptions' => ['class' => 'table'],
            'columns' => [
                'username',
                [
                    'attribute' => 'email',
                    'format' => 'email',
                    'headerOptions' => ['class' => 'd-none d-sm-table-cell'],
                    'contentOptions' => ['class' => 'd-none d-sm-table-cell'],
                ],
                [
                    'attribute' => 'role',
                    'headerOptions' => ['class' => 'd-none d-md-table-cell'],
                    'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                    'value' => function ($model) {
                        $class = $model->role === 'admin' ? 'badge-warning' : 'badge-info';
                        return '<span class="badge ' . $class . '">' . $model->getRoleLabel() . '</span>';
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'status',
                    'headerOptions' => ['class' => 'd-none d-md-table-cell'],
                    'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                    'value' => function ($model) {
                        $class = $model->status == 10 ? 'badge-success' : 'badge-danger';
                        return '<span class="badge ' . $class . '">' . $model->getStatusLabel() . '</span>';
                    },
                    'format' => 'raw',
                ],
                [
                    'headerOptions' => ['class' => 'd-none d-lg-table-cell'],
                    'contentOptions' => ['class' => 'd-none d-lg-table-cell'],
                    'value' => function ($model) { return date('d/m/Y', $model->created_at); },
                ],
                [
                    'attribute' => 'last_login_at',
                    'label' => 'Last Login',
                    'headerOptions' => ['class' => 'd-none d-lg-table-cell'],
                    'contentOptions' => ['class' => 'd-none d-lg-table-cell'],
                    'value' => function ($model) {
                        return $model->last_login_at ? date('d/m/Y H:i', $model->last_login_at) : '—';
                    },
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
                                'title' => $model->status == 10 ? 'Deactivate' : 'Activate',
                            ]);
                        },
                    ],
                ],
            ],
        ]); ?>
    </div>
</div>
