<?php

use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\ScanLogSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Scan Logs';
?>

<div class="page-header">
    <h1><i class="bi bi-bar-chart-fill"></i> Scan Logs</h1>
    <?= yii\helpers\Html::a('<i class="bi bi-filetype-csv"></i> Exportar CSV', ['export-csv'], ['class' => 'btn btn-secondary']) ?>
</div>

<div class="data-card">
    <div class="data-card-body" style="padding: 0;">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'tableOptions' => ['class' => 'table'],
            'columns' => [
                [
                    'attribute' => 'accessed_at',
                    'label' => 'Data',
                    'value' => function ($model) {
                        return date('d/m/Y H:i:s', $model->accessed_at);
                    },
                    'filter' => false,
                ],
                [
                    'attribute' => 'short_url_id',
                    'label' => 'Link (ID)',
                    'value' => function ($model) {
                        if ($model->shortUrl) {
                            $code = '<code style="color:var(--accent-secondary)">' . Html::encode($model->shortUrl->short_code) . '</code>';
                            return $code . ' <span style="font-size:11px;color:var(--text-muted)">(#' . $model->short_url_id . ')</span>';
                        }
                        return '#' . $model->short_url_id;
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'ip_address',
                    'value' => function ($model) {
                        return '<code>' . Html::encode($model->ip_address ?: '—') . '</code>';
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'country',
                    'value' => function ($model) {
                        $loc = Html::encode($model->country ?: '—');
                        if ($model->city) {
                            $loc .= ' <span style="font-size:11px;color:var(--text-muted)">(' . Html::encode($model->city) . ')</span>';
                        }
                        return $loc;
                    },
                    'format' => 'raw',
                    'label' => 'Localização',
                ],
                'device_type',
                'browser',
                [
                    'attribute' => 'language',
                    'label' => 'Idioma',
                    'value' => function ($model) {
                        return strtoupper($model->language ?: '—');
                    }
                ],
                [
                    'attribute' => 'source',
                    'value' => function ($model) {
                        $badgeClass = 'badge-info';
                        if ($model->source === 'qr') $badgeClass = 'badge-success';
                        if ($model->source === 'utm') $badgeClass = 'badge-warning';
                        return '<span class="badge ' . $badgeClass . '">' . Html::encode($model->source ?: 'direct') . '</span>';
                    },
                    'format' => 'raw',
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view}',
                    'buttons' => [
                        'view' => function ($url, $model) {
                            return Html::a('<i class="bi bi-eye"></i>', ['view', 'id' => $model->id], ['class' => 'btn btn-sm btn-secondary']);
                        },
                    ],
                ],
            ],
        ]); ?>
    </div>
</div>
