<?php

use app\models\Campaign;
use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\CampaignSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Campaigns';
?>

<div class="page-header">
    <h1><i class="bi bi-megaphone-fill"></i> Campaigns</h1>
    <?= Html::a('<i class="bi bi-plus-lg"></i> New Campaign', ['create'], ['class' => 'btn btn-primary']) ?>
</div>

<div class="data-card">
    <div class="data-card-body" style="padding: 0;">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'tableOptions' => ['class' => 'table'],
            'columns' => [
                'name',
                [
                    'attribute' => 'description',
                    'headerOptions' => ['class' => 'd-none d-md-table-cell'],
                    'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                    'value' => function ($model) {
                        $desc = $model->description ?: '—';
                        return strlen($desc) > 60 ? substr($desc, 0, 60) . '...' : $desc;
                    },
                ],
                [
                    'label' => 'Links',
                    'headerOptions' => ['class' => 'd-none d-sm-table-cell', 'style' => 'text-align:center'],
                    'contentOptions' => ['class' => 'd-none d-sm-table-cell', 'style' => 'text-align:center'],
                    'value' => function ($model) {
                        return count($model->shortUrls);
                    },
                ],
                [
                    'label' => 'Scans',
                    'headerOptions' => ['class' => 'd-none d-sm-table-cell', 'style' => 'text-align:center'],
                    'contentOptions' => ['class' => 'd-none d-sm-table-cell', 'style' => 'text-align:center'],
                    'value' => function ($model) {
                        return '<strong>' . number_format($model->getTotalScans()) . '</strong>';
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'status',
                    'headerOptions' => ['class' => 'd-none d-md-table-cell'],
                    'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                    'value' => function ($model) {
                        $class = $model->status == Campaign::STATUS_ACTIVE ? 'badge-success' : 'badge-danger';
                        return '<span class="badge ' . $class . '">' . $model->getStatusLabel() . '</span>';
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'created_at',
                    'headerOptions' => ['class' => 'd-none d-lg-table-cell'],
                    'contentOptions' => ['class' => 'd-none d-lg-table-cell'],
                    'value' => function ($model) { return date('d/m/Y', $model->created_at); },
                    'filter' => false,
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view} {update} {delete}',
                    'buttons' => [
                        'view' => function ($url, $model) {
                            return Html::a('<i class="bi bi-eye"></i>', ['view', 'id' => $model->id], ['class' => 'btn btn-sm btn-secondary', 'style' => 'margin-right:4px;']);
                        },
                        'update' => function ($url, $model) {
                            return Html::a('<i class="bi bi-pencil"></i>', ['update', 'id' => $model->id], ['class' => 'btn btn-sm btn-secondary', 'style' => 'margin-right:4px;']);
                        },
                        'delete' => function ($url, $model) {
                            return Html::a('<i class="bi bi-trash"></i>', ['delete', 'id' => $model->id], [
                                'class' => 'btn btn-sm btn-danger',
                                'data' => ['confirm' => 'Are you sure?', 'method' => 'post'],
                            ]);
                        },
                    ],
                ],
            ],
        ]); ?>
    </div>
</div>
