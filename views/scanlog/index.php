<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\ShortUrl;

/** @var yii\web\View $this */
/** @var app\models\ScanLogSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var app\models\ShortUrl $shortUrl */

// Title will be set inside the view logic below
?>

<?php if (isset($shortUrl) && $shortUrl):
    $this->title = 'Logs: ' . ($shortUrl->title ?: $shortUrl->short_code);
?>
<div class="page-header">
    <div>
        <?= Html::a('<i class="bi bi-arrow-left"></i> Back to Links', ['scanlog/index'], ['class' => 'btn btn-sm btn-secondary mb-2']) ?>
        <h1><i class="bi bi-activity"></i> Logs: <?= Html::encode($shortUrl->title ?: $shortUrl->short_code) ?></h1>
        <p class="text-muted mb-0" style="font-size:13px">
            Analisando acessos: <strong><?= Html::encode($shortUrl->getShortUrl()) ?></strong>
        </p>
    </div>
    <div>
        <?= Html::a('<i class="bi bi-filetype-csv"></i> Export CSV', ['scanlog/export-csv', 'short_url_id' => $shortUrl->id], ['class' => 'btn btn-secondary']) ?>
    </div>
</div>
<?php else:
    $this->title = 'Access Logs';
?>
    <div class="alert alert-warning">Link not found or access denied.</div>
<?php endif; ?>

<div class="data-card">
    <div class="data-card-body" style="padding: 0; overflow-x: auto; -webkit-overflow-scrolling: touch;">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel'  => null,
            'tableOptions' => ['class' => 'table scanlog-table'],
            'columns' => [
                [
                    'attribute' => 'accessed_at',
                    'label'     => 'Date',
                    'filter'    => false,
                    'value'     => function ($model) {
                        return date('d/m/Y H:i', $model->accessed_at);
                    },
                ],
                [
                    'attribute'    => 'short_url_title',
                    'label'        => 'Link',
                    'value'        => function ($model) {
                        if ($model->shortUrl) {
                            $title = $model->shortUrl->title ?: $model->shortUrl->short_code;
                            return '<strong>' . Html::encode($title) . '</strong><br>' .
                                   '<code style="font-size:11px;color:var(--accent-secondary)">/' . Html::encode($model->shortUrl->short_code) . '</code>';
                        }
                        return '<span class="text-muted">Unknown</span>';
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute'      => 'ip_address',
                    'headerOptions'  => ['class' => 'd-none d-sm-table-cell'],
                    'contentOptions' => ['class' => 'd-none d-sm-table-cell'],
                    'value'          => function ($model) {
                        return '<code>' . Html::encode($model->ip_address ?: '—') . '</code>';
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute'      => 'country',
                    'label'          => 'Location',
                    'headerOptions'  => ['class' => 'd-none d-md-table-cell'],
                    'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                    'value'          => function ($model) {
                        $loc = Html::encode($model->country ?: '—');
                        if ($model->city) {
                            $loc .= ' <span style="font-size:11px;color:var(--text-muted)">(' . Html::encode($model->city) . ')</span>';
                        }
                        return $loc;
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute'      => 'device_type',
                    'headerOptions'  => ['class' => 'd-none d-lg-table-cell'],
                    'contentOptions' => ['class' => 'd-none d-lg-table-cell'],
                ],
                [
                    'attribute'      => 'browser',
                    'headerOptions'  => ['class' => 'd-none d-lg-table-cell'],
                    'contentOptions' => ['class' => 'd-none d-lg-table-cell'],
                ],
                [
                    'attribute'      => 'source',
                    'headerOptions'  => ['class' => 'd-none d-md-table-cell'],
                    'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                    'value'          => function ($model) {
                        $badgeClass = 'badge-info';
                        if ($model->source === 'qr')  $badgeClass = 'badge-success';
                        if ($model->source === 'utm') $badgeClass = 'badge-warning';
                        return '<span class="badge ' . $badgeClass . '">' . Html::encode($model->source ?: 'direct') . '</span>';
                    },
                    'format' => 'raw',
                ],
                [
                    'class'    => 'yii\grid\ActionColumn',
                    'template' => '{view}',
                    'buttons'  => [
                        'view' => function ($url, $model) {
                            return Html::a('<i class="bi bi-eye"></i>', ['view', 'id' => $model->id], [
                                'class' => 'btn btn-sm btn-secondary',
                                'title' => 'View Details',
                            ]);
                        },
                    ],
                ],
            ],
        ]); ?>
    </div>
</div>

