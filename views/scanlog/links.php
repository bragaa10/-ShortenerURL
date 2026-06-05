<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\ShortUrlSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Scan Logs by Link';
?>

<div class="page-header">
    <div>
        <h1><i class="bi bi-bar-chart-fill"></i> Scan Logs</h1>
        <span class="text-muted" style="font-size:13px;">Selecione um link para ver os detalhes de acesso</span>
    </div>
</div>

<!-- Barra de Busca Global -->
<div class="mb-4">
    <form action="<?= Url::to(['index']) ?>" method="get">
        <div class="d-flex gap-2 flex-wrap">
            <div class="input-group" style="max-width: 450px; flex: 1 1 200px;">
                <span class="input-group-text bg-transparent border-end-0">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" name="ShortUrlSearch[q]" value="<?= Html::encode($searchModel->q) ?>"
                       class="form-control border-start-0" placeholder="Pesquisar links, campanhas...">
                <button type="submit" class="btn btn-primary px-3">Pesquisar</button>
            </div>
            <?php if ($searchModel->q): ?>
                <?= Html::a('<i class="bi bi-x-lg"></i>', ['index'], ['class' => 'btn btn-secondary']) ?>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="data-card">
    <div class="data-card-body" style="padding: 0; overflow-x: auto; -webkit-overflow-scrolling: touch;">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel'  => null,
            'tableOptions' => ['class' => 'table scanlog-table', 'style' => 'margin-bottom:0; min-width: 320px;'],
            'columns' => [
                [
                    'attribute'     => 'title',
                    'label'         => 'Links',
                    'value'         => function ($model) {
                        return '<strong>' . Html::encode($model->title ?: 'Sem título') . '</strong><br>' .
                               '<small class="text-muted">' . Html::encode($model->campaign ? $model->campaign->name : 'Sem campanha') . '</small>';
                    },
                    'format'        => 'raw',
                ],
                [
                    'attribute'      => 'short_code',
                    'label'          => 'Código',
                    'headerOptions'  => ['class' => 'd-none d-md-table-cell'],
                    'contentOptions' => ['class' => 'd-none d-md-table-cell', 'style' => 'white-space:nowrap'],
                    'value'          => function ($model) {
                        return '<code style="color:var(--accent-secondary)">/' . Html::encode($model->short_code) . '</code>';
                    },
                    'format'         => 'raw',
                ],
                [
                    'label'          => 'Total Scans',
                    'headerOptions'  => ['style' => 'text-align:center; white-space:nowrap;'],
                    'contentOptions' => ['style' => 'text-align:center;'],
                    'value'          => function ($model) {
                        return '<span class="badge bg-primary rounded-pill" style="font-size:13px;">' . number_format($model->getTotalScans()) . '</span>';
                    },
                    'format'         => 'raw',
                ],
                [
                    'class'          => 'yii\grid\ActionColumn',
                    'template'       => '{view-logs}',
                    'headerOptions'  => ['style' => 'text-align:right; padding-right:16px; white-space:nowrap;'],
                    'contentOptions' => ['style' => 'text-align:right; padding-right:16px; white-space:nowrap;'],
                    'buttons'        => [
                        'view-logs' => function ($url, $model) {
                            return Html::a('Ver Logs <i class="bi bi-chevron-right"></i>',
                                ['scanlog/index', 'short_url_id' => $model->id],
                                ['class' => 'btn btn-sm btn-outline-primary', 'style' => 'border-radius:20px;']
                            );
                        },
                    ],
                ],
            ],
        ]); ?>
    </div>
</div>

