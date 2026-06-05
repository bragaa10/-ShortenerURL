<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use app\models\Campaign;

/** @var yii\web\View $this */
/** @var app\models\ShortUrlSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Links';
?>

<div class="short-url-index">
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-link-45deg"></i> Links</h1>
        <div>
            <?= Html::a('<i class="bi bi-plus-lg"></i> New Link', ['create'], ['class' => 'btn btn-primary']) ?>
        </div>
    </div>

    <!-- Barra de Busca Global Premium -->
    <div class="search-container mb-4">
        <form action="<?= Url::to(['index']) ?>" method="get" class="d-flex gap-2">
            <div class="input-group" style="max-width: 450px; box-shadow: var(--shadow-sm);">
                <span class="input-group-text bg-transparent border-end-0">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" name="ShortUrlSearch[q]" value="<?= Html::encode($searchModel->q) ?>" 
                       class="form-control border-start-0" placeholder="Search by title, campaign or tags...">
                <button type="submit" class="btn btn-primary px-4">Search</button>
            </div>
            <?php if ($searchModel->q): ?>
                <?= Html::a('<i class="bi bi-x-lg"></i>', ['index'], ['class' => 'btn btn-outline-secondary', 'title' => 'Clear search']) ?>
            <?php endif; ?>
        </form>
    </div>

    <div class="data-card">
        <div class="data-card-body" style="padding: 0;">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => null,
                'tableOptions' => ['class' => 'table', 'style' => 'table-layout: fixed; width: 100%; margin-bottom: 0;'],
                'columns' => [
                    [
                        'attribute' => 'title',
                        'headerOptions' => ['style' => 'width: 25%; min-width: 120px;'],
                        'value' => function ($model) {
                            return '<div class="text-truncate" title="'.Html::encode($model->title).'">'.($model->title ?: '<span class="text-muted">No title</span>').'</div>';
                        },
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'campaign_name',
                        'label' => 'Campaign',
                        'headerOptions' => ['class' => 'd-none d-xl-table-cell', 'style' => 'width: 15%;'],
                        'contentOptions' => ['class' => 'd-none d-xl-table-cell'],
                        'value' => function ($model) {
                            return $model->campaign ? '<span class="text-truncate d-block">' . Html::encode($model->campaign->name) . '</span>' : '<span class="text-muted">—</span>';
                        },
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'short_code',
                        'label' => 'Short URL',
                        'headerOptions' => ['style' => 'width: 240px;'],
                        'contentOptions' => ['style' => 'width: 240px;'],
                        'value' => function ($model) {
                            $fullUrl = $model->getShortUrl();
                            return '
                            <div style="padding: 4px 10px; display: flex; align-items: center; background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: 6px; width: 100%;">
                                <span style="font-family: monospace; font-size: 12px; color: var(--accent-secondary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; flex: 1;">' . Html::encode($fullUrl) . '</span>
                                <button type="button" onclick="copyRowUrl(this, \'' . Html::encode($fullUrl) . '\')" title="Copy" style="background: transparent; border: none; color: var(--text-secondary); cursor: pointer; padding: 0 4px; margin-left: 8px; flex-shrink: 0;">
                                    <i class="bi bi-clipboard" style="font-size: 14px;"></i>
                                </button>
                            </div>';
                        },
                        'format' => 'raw',
                    ],
                    [
                        'label' => 'Scans',
                        'headerOptions' => ['class' => 'd-none d-sm-table-cell', 'style' => 'text-align:center; width: 80px;'],
                        'contentOptions' => ['class' => 'd-none d-sm-table-cell', 'style' => 'text-align:center'],
                        'value' => function ($model) {
                            return '<strong>' . number_format($model->getTotalScans()) . '</strong>';
                        },
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'status',
                        'headerOptions' => ['class' => 'd-none d-md-table-cell', 'style' => 'width: 90px;'],
                        'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                        'value' => function ($model) {
                            return '<span class="badge ' . $model->getStatusBadgeClass() . '">' . $model->getStatusLabel() . '</span>';
                        },
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'created_at',
                        'headerOptions' => ['class' => 'd-none d-lg-table-cell', 'style' => 'width: 100px;'],
                        'contentOptions' => ['class' => 'd-none d-lg-table-cell'],
                        'value' => function ($model) {
                            return date('d/m/Y', $model->created_at);
                        },
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{stats} {logs} {view} {update} {delete}',
                        'headerOptions' => ['style' => 'width: 160px;'],
                        'buttons' => [
                            'stats' => function ($url, $model) {
                                return Html::a('<i class="bi bi-bar-chart-fill"></i>', ['stats', 'id' => $model->id], [
                                    'class' => 'btn btn-sm btn-outline-primary',
                                    'title' => 'Statistics',
                                    'style' => 'margin-right:4px;',
                                ]);
                            },
                            'logs' => function ($url, $model) {
                                return Html::a('<i class="bi bi-activity"></i>', ['scanlog/index', 'short_url_id' => $model->id], [
                                    'class' => 'btn btn-sm btn-outline-info',
                                    'title' => 'Access Logs',
                                    'style' => 'margin-right:4px;',
                                ]);
                            },
                            'view' => function ($url, $model) {
                                return Html::a('<i class="bi bi-eye"></i>', ['view', 'id' => $model->id], [
                                    'class' => 'btn btn-sm btn-secondary',
                                    'title' => 'View',
                                    'style' => 'margin-right:4px;',
                                ]);
                            },
                            'update' => function ($url, $model) {
                                return Html::a('<i class="bi bi-pencil"></i>', ['update', 'id' => $model->id], [
                                    'class' => 'btn btn-sm btn-secondary',
                                    'title' => 'Edit',
                                    'style' => 'margin-right:4px;',
                                ]);
                            },
                            'delete' => function ($url, $model) {
                                return Html::a('<i class="bi bi-trash"></i>', ['delete', 'id' => $model->id], [
                                    'class' => 'btn btn-sm btn-danger',
                                    'title' => 'Delete',
                                    'data' => ['confirm' => 'Are you sure you want to delete this link?', 'method' => 'post'],
                                ]);
                            },
                        ],
                    ],
                ],
            ]); ?>
        </div>
    </div>
</div>

<script>
function copyRowUrl(btn, url) {
    navigator.clipboard.writeText(url).then(function () {
        var icon = btn.querySelector('i');
        var originalClass = icon.className;
        icon.className = 'bi bi-check-lg';
        icon.style.color = 'var(--accent-success)';
        
        btn.classList.add('active');
        
        setTimeout(function() {
            icon.className = originalClass;
            icon.style.color = '';
            btn.classList.remove('active');
        }, 2000);
    });
}
</script>
