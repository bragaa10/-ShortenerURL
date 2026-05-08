<?php

use app\models\Campaign;
use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\CampaignSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Campanhas';
?>

<div class="page-header">
    <h1><i class="bi bi-megaphone-fill"></i> Campanhas</h1>
    <?= Html::a('<i class="bi bi-plus-lg"></i> Nova Campanha', ['create'], ['class' => 'btn btn-primary']) ?>
</div>

<div class="data-card">
    <div class="data-card-body" style="padding: 0;">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'tableOptions' => ['class' => 'table'],
            'columns' => [
                'name',
                [
                    'attribute' => 'description',
                    'value' => function ($model) {
                        $desc = $model->description ?: '—';
                        return strlen($desc) > 60 ? substr($desc, 0, 60) . '...' : $desc;
                    },
                ],
                [
                    'label' => 'Links',
                    'value' => function ($model) {
                        return count($model->shortUrls);
                    },
                    'contentOptions' => ['style' => 'text-align:center'],
                    'headerOptions' => ['style' => 'text-align:center'],
                ],
                [
                    'label' => 'Scans',
                    'value' => function ($model) {
                        return '<strong>' . number_format($model->getTotalScans()) . '</strong>';
                    },
                    'format' => 'raw',
                    'contentOptions' => ['style' => 'text-align:center'],
                    'headerOptions' => ['style' => 'text-align:center'],
                ],
                [
                    'attribute' => 'status',
                    'value' => function ($model) {
                        $class = $model->status == Campaign::STATUS_ACTIVE ? 'badge-success' : 'badge-danger';
                        return '<span class="badge ' . $class . '">' . $model->getStatusLabel() . '</span>';
                    },
                    'format' => 'raw',
                    'filter' => [1 => 'Ativa', 0 => 'Inativa'],
                ],
                [
                    'attribute' => 'created_at',
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
                                'data' => ['confirm' => 'Tem a certeza?', 'method' => 'post'],
                            ]);
                        },
                    ],
                ],
            ],
        ]); ?>
    </div>
</div>
