<?php

use app\models\ShortUrl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\ShortUrlSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Links';
?>

<div class="page-header">
    <h1><i class="bi bi-link-45deg"></i> Links</h1>
    <?= Html::a('<i class="bi bi-plus-lg"></i> Novo Link', ['create'], ['class' => 'btn btn-primary']) ?>
</div>

<div class="data-card">
    <div class="data-card-body" style="padding: 0;">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'tableOptions' => ['class' => 'table'],
            'columns' => [
                [
                    'attribute' => 'title',
                    'value' => function ($model) {
                        return $model->title ?: '<span style="color:var(--text-muted)">Sem título</span>';
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'short_code',
                    'label' => 'URL Curto',
                    'value' => function ($model) {
                        $url = $model->getShortUrl();
                        return '<code style="color:var(--accent-secondary)">' . Html::encode($model->short_code) . '</code>';
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'original_url',
                    'value' => function ($model) {
                        $url = $model->original_url;
                        $short = strlen($url) > 50 ? substr($url, 0, 50) . '...' : $url;
                        return '<span title="' . Html::encode($url) . '">' . Html::encode($short) . '</span>';
                    },
                    'format' => 'raw',
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
                        return '<span class="badge ' . $model->getStatusBadgeClass() . '">' . $model->getStatusLabel() . '</span>';
                    },
                    'format' => 'raw',
                    'filter' => [1 => 'Ativo', 0 => 'Inativo'],
                ],
                [
                    'attribute' => 'created_at',
                    'value' => function ($model) {
                        return date('d/m/Y', $model->created_at);
                    },
                    'filter' => false,
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{stats} {view} {update} {delete}',
                    'buttons' => [
                        'stats' => function ($url, $model) {
                            return Html::a('<i class="bi bi-bar-chart-fill"></i>', ['stats', 'id' => $model->id], [
                                'class' => 'btn btn-sm btn-outline-primary',
                                'title' => 'Estatísticas',
                                'style' => 'margin-right:4px;',
                            ]);
                        },
                        'view' => function ($url, $model) {
                            return Html::a('<i class="bi bi-eye"></i>', ['view', 'id' => $model->id], [
                                'class' => 'btn btn-sm btn-secondary',
                                'title' => 'Ver',
                                'style' => 'margin-right:4px;',
                            ]);
                        },
                        'update' => function ($url, $model) {
                            return Html::a('<i class="bi bi-pencil"></i>', ['update', 'id' => $model->id], [
                                'class' => 'btn btn-sm btn-secondary',
                                'title' => 'Editar',
                                'style' => 'margin-right:4px;',
                            ]);
                        },
                        'delete' => function ($url, $model) {
                            return Html::a('<i class="bi bi-trash"></i>', ['delete', 'id' => $model->id], [
                                'class' => 'btn btn-sm btn-danger',
                                'title' => 'Eliminar',
                                'data' => ['confirm' => 'Tem a certeza que deseja eliminar este link?', 'method' => 'post'],
                            ]);
                        },
                    ],
                ],
            ],
        ]); ?>
    </div>
</div>
