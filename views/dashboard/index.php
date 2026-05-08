<?php

/** @var yii\web\View $this */

use yii\helpers\Html;

$this->title = 'Dashboard';
?>

<div class="page-header">
    <h1><i class="bi bi-grid-1x2-fill"></i> Dashboard</h1>
</div>

<!-- Stat Cards -->
<div class="stat-cards">
    <div class="stat-card">
        <div class="stat-icon primary"><i class="bi bi-link-45deg"></i></div>
        <div class="stat-value"><?= number_format($totalLinks) ?></div>
        <div class="stat-label">Total de Links</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon secondary"><i class="bi bi-cursor-fill"></i></div>
        <div class="stat-value"><?= number_format($totalScans) ?></div>
        <div class="stat-label">Total de Scans</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon success"><i class="bi bi-qr-code"></i></div>
        <div class="stat-value"><?= number_format($totalQrCodes) ?></div>
        <div class="stat-label">QR Codes</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon danger"><i class="bi bi-lightning-fill"></i></div>
        <div class="stat-value"><?= number_format($scansToday) ?></div>
        <div class="stat-label">Scans Hoje</div>
    </div>
</div>

<!-- Daily Scans Chart -->
<div class="data-card">
    <div class="data-card-header">
        <h3><i class="bi bi-graph-up"></i> Scans — Últimos 30 dias</h3>
        <span style="color: var(--text-secondary); font-size: 13px;">
            <?= number_format($uniqueScans) ?> visitantes únicos
        </span>
    </div>
    <div class="data-card-body">
        <div class="chart-container">
            <canvas id="dailyScansChart"></canvas>
        </div>
    </div>
</div>

<div class="grid-2">
    <!-- Top Links -->
    <div class="data-card">
        <div class="data-card-header">
            <h3><i class="bi bi-trophy-fill"></i> Links Mais Acedidos</h3>
        </div>
        <div class="data-card-body" style="padding: 0;">
            <table class="table" style="margin: 0;">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Código</th>
                        <th>Scans</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($topLinks)): ?>
                        <tr><td colspan="3" style="text-align:center; color: var(--text-muted); padding: 32px;">Sem dados</td></tr>
                    <?php else: ?>
                        <?php foreach ($topLinks as $link): ?>
                        <tr>
                            <td><?= Html::encode($link['title'] ?: 'Sem título') ?></td>
                            <td><code style="color: var(--accent-secondary);"><?= Html::encode($link['short_code']) ?></code></td>
                            <td><strong><?= number_format($link['scan_count']) ?></strong></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Top Campaigns -->
    <div class="data-card">
        <div class="data-card-header">
            <h3><i class="bi bi-megaphone-fill"></i> Top Campanhas</h3>
        </div>
        <div class="data-card-body" style="padding: 0;">
            <table class="table" style="margin: 0;">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Links</th>
                        <th>Scans</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($topCampaigns)): ?>
                        <tr><td colspan="3" style="text-align:center; color: var(--text-muted); padding: 32px;">Sem dados</td></tr>
                    <?php else: ?>
                        <?php foreach ($topCampaigns as $campaign): ?>
                        <tr>
                            <td><?= Html::encode($campaign['name']) ?></td>
                            <td><?= number_format($campaign['link_count']) ?></td>
                            <td><strong><?= number_format($campaign['scan_count']) ?></strong></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="grid-2">
    <!-- Devices Chart -->
    <div class="data-card">
        <div class="data-card-header">
            <h3><i class="bi bi-phone-fill"></i> Dispositivos</h3>
        </div>
        <div class="data-card-body">
            <div class="chart-container" style="max-height: 250px;">
                <canvas id="devicesChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Browsers Chart -->
    <div class="data-card">
        <div class="data-card-header">
            <h3><i class="bi bi-globe2"></i> Browsers</h3>
        </div>
        <div class="data-card-body">
            <div class="chart-container" style="max-height: 250px;">
                <canvas id="browsersChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Countries -->
<?php if (!empty($countries)): ?>
<div class="data-card">
    <div class="data-card-header">
        <h3><i class="bi bi-geo-alt-fill"></i> Países</h3>
    </div>
    <div class="data-card-body" style="padding: 0;">
        <table class="table" style="margin: 0;">
            <thead>
                <tr><th>País</th><th>Scans</th></tr>
            </thead>
            <tbody>
                <?php foreach ($countries as $country): ?>
                <tr>
                    <td><?= Html::encode($country['country']) ?></td>
                    <td><strong><?= number_format($country['country_count']) ?></strong></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chartColors = {
        primary: '#6366f1',
        primaryBg: 'rgba(99, 102, 241, 0.1)',
        secondary: '#06b6d4',
        success: '#10b981',
        warning: '#f59e0b',
        danger: '#ef4444',
        purple: '#8b5cf6',
        grid: 'rgba(255,255,255,0.05)',
        text: '#8b8fa3'
    };

    const defaultOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { labels: { color: chartColors.text, font: { family: "'Inter'" } } }
        }
    };

    // Daily Scans Line Chart
    new Chart(document.getElementById('dailyScansChart'), {
        type: 'line',
        data: {
            labels: <?= json_encode($chartLabels) ?>,
            datasets: [{
                label: 'Scans',
                data: <?= json_encode($chartData) ?>,
                borderColor: chartColors.primary,
                backgroundColor: chartColors.primaryBg,
                fill: true,
                tension: 0.4,
                borderWidth: 2,
                pointRadius: 3,
                pointBackgroundColor: chartColors.primary,
            }]
        },
        options: {
            ...defaultOptions,
            scales: {
                x: { grid: { color: chartColors.grid }, ticks: { color: chartColors.text, font: { size: 11 } } },
                y: { grid: { color: chartColors.grid }, ticks: { color: chartColors.text, font: { size: 11 } }, beginAtZero: true }
            },
            plugins: { legend: { display: false } }
        }
    });

    // Devices Doughnut
    const deviceLabels = <?= json_encode(array_column($devices, 'device_type')) ?>;
    const deviceData = <?= json_encode(array_map('intval', array_column($devices, 'device_count'))) ?>;
    if (deviceLabels.length > 0) {
        new Chart(document.getElementById('devicesChart'), {
            type: 'doughnut',
            data: {
                labels: deviceLabels,
                datasets: [{
                    data: deviceData,
                    backgroundColor: [chartColors.primary, chartColors.secondary, chartColors.warning, chartColors.danger],
                    borderWidth: 0
                }]
            },
            options: { ...defaultOptions, cutout: '65%' }
        });
    }

    // Browsers Doughnut
    const browserLabels = <?= json_encode(array_column($browsers, 'browser')) ?>;
    const browserData = <?= json_encode(array_map('intval', array_column($browsers, 'browser_count'))) ?>;
    if (browserLabels.length > 0) {
        new Chart(document.getElementById('browsersChart'), {
            type: 'doughnut',
            data: {
                labels: browserLabels,
                datasets: [{
                    data: browserData,
                    backgroundColor: [chartColors.primary, chartColors.success, chartColors.warning, chartColors.secondary, chartColors.purple, chartColors.danger],
                    borderWidth: 0
                }]
            },
            options: { ...defaultOptions, cutout: '65%' }
        });
    }
});
</script>
