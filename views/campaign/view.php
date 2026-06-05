<?php
use yii\helpers\Html;

/** @var app\models\Campaign $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Campaigns', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="page-header">
    <h1><i class="bi bi-megaphone-fill"></i> <?= Html::encode($this->title) ?></h1>
    <div style="display:flex; gap:8px;">
        <?= Html::a('<i class="bi bi-bar-chart-fill"></i> Statistics', ['stats', 'id' => $model->id], ['class' => 'btn btn-info']) ?>
        <?= Html::a('<i class="bi bi-pencil"></i> Edit', ['update', 'id' => $model->id], ['class' => 'btn btn-secondary']) ?>
        <?= Html::a('<i class="bi bi-trash"></i> Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => ['confirm' => 'Are you sure?', 'method' => 'post'],
        ]) ?>
    </div>
</div>

<div class="grid-2">
    <div class="data-card">
        <div class="data-card-header"><h3>Details</h3></div>
        <div class="data-card-body">
            <table class="table detail-view">
                <tr><th>Name</th><td><?= Html::encode($model->name) ?></td></tr>
                <tr><th>Description</th><td><?= Html::encode($model->description ?: '—') ?></td></tr>
                <tr>
                    <th>Status</th>
                    <td><span class="badge <?= $model->status ? 'badge-success' : 'badge-danger' ?>"><?= $model->getStatusLabel() ?></span></td>
                </tr>
                <tr><th>Total Links</th><td><strong><?= count($model->shortUrls) ?></strong></td></tr>
                <tr><th>Total Scans</th><td><strong><?= number_format($model->getTotalScans()) ?></strong></td></tr>
                <tr><th>Created At</th><td><?= date('d/m/Y H:i', $model->created_at) ?></td></tr>
            </table>
        </div>
    </div>

    <div class="data-card">
        <div class="data-card-header"><h3>Links in this Campaign</h3></div>
        <div class="data-card-body" style="padding:0;">
            <table class="table" style="margin:0;">
                <thead><tr><th>Title</th><th>Code</th><th>Scans</th><th>Status</th></tr></thead>
                <tbody>
                <?php if (empty($model->shortUrls)): ?>
                    <tr><td colspan="4" style="text-align:center;color:var(--text-muted);padding:24px;">No links associated</td></tr>
                <?php else: ?>
                    <?php foreach ($model->shortUrls as $link): ?>
                    <tr>
                        <td><?= Html::a(Html::encode($link->title ?: 'No title'), ['/short-url/view', 'id' => $link->id]) ?></td>
                        <td><code style="color:var(--accent-secondary)"><?= Html::encode($link->short_code) ?></code></td>
                        <td><strong><?= $link->getTotalScans() ?></strong></td>
                        <td><span class="badge <?= $link->getStatusBadgeClass() ?>"><?= $link->getStatusLabel() ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
