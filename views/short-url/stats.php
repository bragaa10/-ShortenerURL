<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\ShortUrl $model */
/** @var array $chartLabels */
/** @var array $chartData */
/** @var array $countries */
/** @var array $devices */
/** @var array $browsers */
/** @var array $operatingSystems */
/** @var array $referers */
/** @var array $sources */
/** @var app\models\ScanLog[] $recentScans */

$this->title = 'Statistics: ' . ($model->title ?: $model->short_code);
$this->params['breadcrumbs'][] = ['label' => 'Links', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title ?: $model->short_code, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Statistics';
?>

<div class="page-header">
    <h1><i class="bi bi-bar-chart-fill"></i> <?= Html::encode($this->title) ?></h1>
    <?= Html::a('<i class="bi bi-arrow-left"></i> Back', ['view', 'id' => $model->id], ['class' => 'btn btn-secondary']) ?>
</div>

<!-- Stats Summary -->
<div class="stat-cards">
    <div class="stat-card">
        <div class="stat-icon primary"><i class="bi bi-cursor-fill"></i></div>
        <div class="stat-value"><?= number_format($model->getTotalScans()) ?></div>
        <div class="stat-label">Total Scans</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon secondary"><i class="bi bi-people-fill"></i></div>
        <div class="stat-value"><?= number_format($model->getUniqueScans()) ?></div>
        <div class="stat-label">Unique Visitors</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon success"><i class="bi bi-link-45deg"></i></div>
        <div class="stat-value"><code style="font-size:16px;color:var(--accent-secondary)"><?= Html::encode($model->short_code) ?></code></div>
        <div class="stat-label">Short Code</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon danger"><i class="bi bi-clock-fill"></i></div>
        <div class="stat-value"><?= date('d/m/Y', $model->created_at) ?></div>
        <div class="stat-label">Created At</div>
    </div>
</div>

<!-- Daily Chart -->
<div class="data-card">
    <div class="data-card-header">
        <h3><i class="bi bi-graph-up"></i> Scans per Day — Last 30 days</h3>
    </div>
    <div class="data-card-body">
        <div class="chart-container">
            <canvas id="dailyChart"></canvas>
        </div>
    </div>
</div>

<div class="grid-2">
    <!-- Devices -->
    <div class="data-card">
        <div class="data-card-header"><h3><i class="bi bi-phone-fill"></i> Devices</h3></div>
        <div class="data-card-body">
            <?php if (empty($devices)): ?>
                <p style="color:var(--text-muted);text-align:center;padding:20px;">No data</p>
            <?php else: ?>
                <div class="chart-container" style="max-height:220px"><canvas id="devChart"></canvas></div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Browsers -->
    <div class="data-card">
        <div class="data-card-header"><h3><i class="bi bi-globe2"></i> Browsers</h3></div>
        <div class="data-card-body">
            <?php if (empty($browsers)): ?>
                <p style="color:var(--text-muted);text-align:center;padding:20px;">No data</p>
            <?php else: ?>
                <div class="chart-container" style="max-height:220px"><canvas id="brChart"></canvas></div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="grid-2">
    <div class="data-card">
        <div class="data-card-header"><h3><i class="bi bi-diagram-3-fill"></i> Access Source</h3></div>
        <div class="data-card-body">
            <?php if (empty($sources)): ?>
                <p style="color:var(--text-muted);text-align:center;padding:20px;">No data</p>
            <?php else: ?>
                <div class="chart-container" style="max-height:220px"><canvas id="srcChart"></canvas></div>
            <?php endif; ?>
        </div>
    </div>

    <div class="data-card">
        <div class="data-card-header"><h3><i class="bi bi-laptop"></i> Operating Systems</h3></div>
        <div class="data-card-body" style="padding:0;">
            <table class="table" style="margin:0;">
                <thead><tr><th>OS</th><th>Scans</th></tr></thead>
                <tbody>
                <?php if (empty($operatingSystems)): ?>
                    <tr><td colspan="2" style="text-align:center;color:var(--text-muted);padding:24px;">No data</td></tr>
                <?php else: ?>
                    <?php foreach ($operatingSystems as $os): ?>
                    <tr><td><?= Html::encode($os['os']) ?></td><td><strong><?= $os['cnt'] ?></strong></td></tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="grid-2">
    <div class="data-card">
        <div class="data-card-header"><h3><i class="bi bi-geo-alt-fill"></i> Countries</h3></div>
        <div class="data-card-body" style="padding:0;">
            <table class="table" style="margin:0;">
                <thead><tr><th>Country</th><th>Scans</th></tr></thead>
                <tbody>
                <?php if (empty($countries)): ?>
                    <tr><td colspan="2" style="text-align:center;color:var(--text-muted);padding:24px;">No data</td></tr>
                <?php else: ?>
                    <?php foreach ($countries as $c): ?>
                    <tr><td><?= Html::encode($c['country']) ?></td><td><strong><?= $c['cnt'] ?></strong></td></tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Referers -->
    <div class="data-card">
        <div class="data-card-header"><h3><i class="bi bi-box-arrow-in-right"></i> Referers</h3></div>
        <div class="data-card-body" style="padding:0;">
            <table class="table" style="margin:0;">
                <thead><tr><th>Referer</th><th>Scans</th></tr></thead>
                <tbody>
                <?php if (empty($referers)): ?>
                    <tr><td colspan="2" style="text-align:center;color:var(--text-muted);padding:24px;">No data</td></tr>
                <?php else: ?>
                    <?php foreach ($referers as $ref): ?>
                    <tr><td style="font-size:12px;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= Html::encode($ref['referer']) ?></td><td><strong><?= $ref['cnt'] ?></strong></td></tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Recent Scans -->
<div class="data-card">
    <div class="data-card-header"><h3><i class="bi bi-clock-history"></i> Recent Scans</h3></div>
    <div class="data-card-body" style="padding:0;">
        <div class="table-responsive">
        <table class="table" style="margin:0;">
            <thead>
                <tr><th>Date</th><th>IP</th><th>Country</th><th>Device</th><th>Browser</th><th>OS</th><th>Source</th></tr>
            </thead>
            <tbody>
            <?php if (empty($recentScans)): ?>
                <tr><td colspan="7" style="text-align:center;color:var(--text-muted);padding:24px;">No scans recorded</td></tr>
            <?php else: ?>
                <?php foreach ($recentScans as $scan): ?>
                <tr>
                    <td><?= date('d/m/Y H:i', $scan->accessed_at) ?></td>
                    <td><code><?= Html::encode($scan->ip_address) ?></code></td>
                    <td><?= Html::encode($scan->country ?: '—') ?></td>
                    <td><?= Html::encode($scan->device_type ?: '—') ?></td>
                    <td><?= Html::encode($scan->browser ?: '—') ?></td>
                    <td><?= Html::encode($scan->os ?: '—') ?></td>
                    <td><span class="badge badge-info"><?= Html::encode($scan->source ?: 'direct') ?></span></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const colors = {
        primary: '#6366f1', primaryBg: 'rgba(99,102,241,0.1)',
        secondary: '#06b6d4', success: '#10b981', warning: '#f59e0b',
        danger: '#ef4444', purple: '#8b5cf6',
        grid: 'rgba(255,255,255,0.05)', text: '#8b8fa3'
    };

    new Chart(document.getElementById('dailyChart'), {
        type: 'line',
        data: {
            labels: <?= json_encode($chartLabels) ?>,
            datasets: [{ label: 'Scans', data: <?= json_encode($chartData) ?>,
                borderColor: colors.primary, backgroundColor: colors.primaryBg,
                fill: true, tension: 0.4, borderWidth: 2, pointRadius: 3, pointBackgroundColor: colors.primary }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            scales: {
                x: { grid: { color: colors.grid }, ticks: { color: colors.text } },
                y: { grid: { color: colors.grid }, ticks: { color: colors.text }, beginAtZero: true }
            },
            plugins: { legend: { display: false } }
        }
    });

    <?php if (!empty($devices)): ?>
    new Chart(document.getElementById('devChart'), {
        type: 'doughnut',
        data: {
            labels: <?= json_encode(array_column($devices, 'device_type')) ?>,
            datasets: [{ data: <?= json_encode(array_map('intval', array_column($devices, 'cnt'))) ?>,
                backgroundColor: [colors.primary, colors.secondary, colors.warning, colors.danger], borderWidth: 0 }]
        },
        options: { responsive: true, maintainAspectRatio: false, cutout: '65%',
            plugins: { legend: { labels: { color: colors.text } } } }
    });
    <?php endif; ?>

    <?php if (!empty($browsers)): ?>
    new Chart(document.getElementById('brChart'), {
        type: 'doughnut',
        data: {
            labels: <?= json_encode(array_column($browsers, 'browser')) ?>,
            datasets: [{ data: <?= json_encode(array_map('intval', array_column($browsers, 'cnt'))) ?>,
                backgroundColor: [colors.primary, colors.success, colors.warning, colors.secondary, colors.purple, colors.danger], borderWidth: 0 }]
        },
        options: { responsive: true, maintainAspectRatio: false, cutout: '65%',
            plugins: { legend: { labels: { color: colors.text } } } }
    });
    <?php endif; ?>

    <?php if (!empty($sources)): ?>
    new Chart(document.getElementById('srcChart'), {
        type: 'doughnut',
        data: {
            labels: <?= json_encode(array_map(function($s){ return ucfirst($s['source'] ?: 'direct'); }, $sources)) ?>,
            datasets: [{ data: <?= json_encode(array_map('intval', array_column($sources, 'cnt'))) ?>,
                backgroundColor: [colors.secondary, colors.success, colors.warning, colors.primary], borderWidth: 0 }]
        },
        options: { responsive: true, maintainAspectRatio: false, cutout: '65%',
            plugins: { legend: { labels: { color: colors.text } } } }
    });
    <?php endif; ?>
});
</script>
