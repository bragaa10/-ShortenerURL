<?php

use yii\helpers\Html;

/** @var app\models\ScanLog $model */

$this->title = 'Scan #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Scan Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="page-header">
    <h1><i class="bi bi-eye"></i> <?= Html::encode($this->title) ?></h1>
    <?= Html::a('<i class="bi bi-arrow-left"></i> Voltar', ['index'], ['class' => 'btn btn-secondary']) ?>
</div>

<div class="data-card">
    <div class="data-card-header"><h3>Detalhes do Acesso</h3></div>
    <div class="data-card-body">
        <table class="table detail-view">
            <tr><th>ID</th><td><?= $model->id ?></td></tr>
            <tr>
                <th>Link</th>
                <td>
                    <?php if ($model->shortUrl): ?>
                        <?= Html::a(Html::encode($model->shortUrl->short_code), ['/short-url/view', 'id' => $model->short_url_id]) ?>
                        — <?= Html::encode($model->shortUrl->title ?: $model->shortUrl->original_url) ?>
                    <?php else: ?>
                        #<?= $model->short_url_id ?>
                    <?php endif; ?>
                </td>
            </tr>
            <tr><th>Data/Hora</th><td><?= date('d/m/Y H:i:s', $model->accessed_at) ?></td></tr>
            <tr><th>IP</th><td><code><?= Html::encode($model->ip_address ?: '—') ?></code></td></tr>
            <tr><th>País</th><td><?= Html::encode($model->country ?: '—') ?></td></tr>
            <tr><th>Cidade</th><td><?= Html::encode($model->city ?: '—') ?></td></tr>
            <tr><th>Dispositivo</th><td><?= Html::encode($model->device_type ?: '—') ?></td></tr>
            <tr><th>Sistema Operativo</th><td><?= Html::encode($model->os ?: '—') ?></td></tr>
            <tr><th>Browser</th><td><?= Html::encode($model->browser ?: '—') ?></td></tr>
            <tr><th>Idioma</th><td><?= Html::encode($model->language ?: '—') ?></td></tr>
            <tr><th>Fonte</th><td><span class="badge badge-info"><?= Html::encode($model->source ?: 'direct') ?></span></td></tr>
            <tr><th>Referer</th><td style="word-break:break-all;"><?= Html::encode($model->referer ?: '—') ?></td></tr>
            <tr><th>User Agent</th><td style="word-break:break-all; font-size:12px;"><?= Html::encode($model->user_agent ?: '—') ?></td></tr>
            <?php if ($model->utm_source): ?>
            <tr><th>UTM Source</th><td><?= Html::encode($model->utm_source) ?></td></tr>
            <?php endif; ?>
            <?php if ($model->utm_medium): ?>
            <tr><th>UTM Medium</th><td><?= Html::encode($model->utm_medium) ?></td></tr>
            <?php endif; ?>
            <?php if ($model->utm_campaign): ?>
            <tr><th>UTM Campaign</th><td><?= Html::encode($model->utm_campaign) ?></td></tr>
            <?php endif; ?>
            <?php if ($model->utm_term): ?>
            <tr><th>UTM Term</th><td><?= Html::encode($model->utm_term) ?></td></tr>
            <?php endif; ?>
            <?php if ($model->utm_content): ?>
            <tr><th>UTM Content</th><td><?= Html::encode($model->utm_content) ?></td></tr>
            <?php endif; ?>
        </table>
    </div>
</div>
