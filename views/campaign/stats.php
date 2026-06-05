<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Campaign $model */
/** @var array $chartLabels */
/** @var array $chartData */
/** @var int $totalScans */
/** @var int $uniqueScans */
/** @var array $topLinks */
/** @var array $devices */
/** @var array $browsers */
/** @var array $countries */

$this->title = 'Campaign Statistics: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Campaigns', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Statistics';
?>

<div class="page-header">
    <h1><i class="bi bi-bar-chart-steps"></i> <?= Html::encode($this->title) ?></h1>
    <?= Html::a('<i class="bi bi-arrow-left"></i> Back', ['view', 'id' => $model->id], ['class' => 'btn btn-secondary']) ?>
</div>

<!-- Stats Summary -->
<div class="stat-cards">
    <div class="stat-card">
        <div class="stat-icon primary"><i class="bi bi-cursor-fill"></i></div>
        <div class="stat-value"><?= number_format($totalScans) ?></div>
        <div class="stat-label">Total Scans</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon secondary"><i class="bi bi-people-fill"></i></div>
        <div class="stat-value"><?= number_format($uniqueScans) ?></div>
        <div class="stat-label">Unique Visitors</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon success"><i class="bi bi-link-45deg"></i></div>
        <div class="stat-value"><?= count($model->shortUrls) ?></div>
        <div class="stat-label">Links in Campaign</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon danger"><i class="bi bi-calendar-check"></i></div>
        <div class="stat-value"><?= date('d/m/Y', $model->created_at) ?></div>
        <div class="stat-label">Created At</div>
    </div>
</div>

<!-- Daily Chart -->
<div class="data-card">
    <div class="data-card-header">
        <h3><i class="bi bi-graph-up"></i> Aggregated Scans — Last 30 days</h3>
    </div>
    <div class="data-card-body">
        <div class="chart-container">
            <canvas id="dailyChart"></canvas>
        </div>
    </div>
</div>

<div class="grid-2">
    <!-- Top Links in this Campaign -->
    <div class="data-card">
        <div class="data-card-header"><h3><i class="bi bi-award-fill"></i> Top Links (Performance)</h3></div>
        <div class="data-card-body" style="padding:0;">
            <table class="table" style="margin:0;">
                <thead><tr><th>Link / Code</th><th>Scans</th></tr></thead>
                <tbody>
                <?php if (empty($topLinks)): ?>
                    <tr><td colspan="2" style="text-align:center;color:var(--text-muted);padding:24px;">No active links</td></tr>
                <?php else: ?>
                    <?php foreach ($topLinks as $link): ?>
                    <tr>
                        <td>
                            <div style="font-weight:600;"><?= Html::encode($link['title'] ?: $link['short_code']) ?></div>
                            <code style="font-size:12px;color:var(--text-muted)"><?= $link['short_code'] ?></code>
                        </td>
                        <td><span class="badge badge-info"><?= $link['scan_count'] ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Countries -->
    <div class="data-card">
        <div class="data-card-header"><h3><i class="bi bi-geo-alt-fill"></i> Geographic Distribution</h3></div>
        <div class="data-card-body" style="padding:0;">
            <table class="table" style="margin:0;">
                <thead><tr><th>Country</th><th>Scans</th></tr></thead>
                <tbody>
                <?php if (empty($countries)): ?>
                    <tr><td colspan="2" style="text-align:center;color:var(--text-muted);padding:24px;">No data</td></tr>
                <?php else: ?>
                    <?php foreach ($countries as $c): ?>
                    <tr>
                        <td><?= Html::encode($c['country']) ?></td>
                        <td><strong><?= $c['country_count'] ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
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
            datasets: [{ data: <?= json_encode(array_map('intval', array_column($devices, 'device_count'))) ?>,
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
            datasets: [{ data: <?= json_encode(array_map('intval', array_column($browsers, 'browser_count'))) ?>,
                backgroundColor: [colors.primary, colors.success, colors.warning, colors.secondary, colors.purple, colors.danger], borderWidth: 0 }]
        },
        options: { responsive: true, maintainAspectRatio: false, cutout: '65%',
            plugins: { legend: { labels: { color: colors.text } } } }
    });
    <?php endif; ?>
});
</script>
