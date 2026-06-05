<?php
use yii\helpers\Html;

/** @var array $chartLabels */
/** @var array $chartValues */
/** @var int $totalScans */
/** @var int $uniqueScans */
/** @var array $countries */
/** @var array $devices */
/** @var string $reportDate */
/** @var string $period */

// Generate QuickChart URL for the daily scans graph
$chartConfig = [
    'type' => 'line',
    'data' => [
        'labels' => $chartLabels,
        'datasets' => [[
            'label' => 'Daily Scans',
            'data' => $chartValues,
            'fill' => true,
            'backgroundColor' => 'rgba(99, 102, 241, 0.1)',
            'borderColor' => '#6366f1',
            'borderWidth' => 2,
            'pointRadius' => 2
        ]]
    ],
    'options' => [
        'title' => ['display' => true, 'text' => 'Activity - Last 30 Days'],
        'scales' => [
            'yAxes' => [['ticks' => ['beginAtZero' => true]]]
        ]
    ]
];
$chartUrl = "https://quickchart.io/chart?c=" . urlencode(json_encode($chartConfig));
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #1e293b; line-height: 1.5; margin: 0; padding: 0; }
        .header { background: #f8fafc; padding: 40px; border-bottom: 2px solid #e2e8f0; text-align: center; }
        .logo { color: #6366f1; font-size: 32px; font-weight: bold; margin-bottom: 10px; }
        .report-title { font-size: 24px; font-weight: bold; color: #0f172a; }
        .report-meta { font-size: 14px; color: #64748b; margin-top: 5px; }
        
        .container { padding: 40px; }
        .grid { width: 100%; margin-bottom: 30px; }
        .grid td { vertical-align: top; }
        
        .card { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin-bottom: 20px; }
        .card-title { font-size: 16px; font-weight: bold; margin-bottom: 15px; color: #475569; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px; }
        
        .stat-box { text-align: center; padding: 20px; border: 1px solid #f1f5f9; border-radius: 6px; }
        .stat-value { font-size: 28px; font-weight: bold; color: #6366f1; }
        .stat-label { font-size: 12px; color: #64748b; text-transform: uppercase; letter-spacing: 1px; }
        
        .chart-img { width: 100%; height: auto; margin: 20px 0; border-radius: 8px; }
        
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 10px; background: #f8fafc; font-size: 12px; color: #64748b; text-transform: uppercase; }
        td { padding: 12px 10px; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
        
        .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 11px; background: #e0e7ff; color: #4338ca; }
        
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #94a3b8; padding: 20px; border-top: 1px solid #f1f5f9; }
    </style>
</head>
<body>

<?php
$logoPath = Yii::getAlias('@webroot') . '/img/logo_jornal.svg';
$logoData = '';
if (file_exists($logoPath)) {
    $logoData = 'data:image/svg+xml;base64,' . base64_encode(file_get_contents($logoPath));
}
?>
<div class="header">
    <?php if ($logoData): ?>
        <img src="<?= $logoData ?>" style="height: 60px; margin-bottom: 10px;">
    <?php else: ?>
        <div class="logo">UrlShortener Pro</div>
    <?php endif; ?>
    <div class="report-title">Performance Analytics Report</div>
    <div class="report-meta">Generated on <?= $reportDate ?> • Period: <?= $period ?></div>
</div>

<div class="container">
    <!-- Key Metrics -->
    <table class="grid">
        <tr>
            <td width="50%" style="padding-right: 10px;">
                <div class="stat-box">
                    <div class="stat-value"><?= number_format($totalScans) ?></div>
                    <div class="stat-label">Total Scans</div>
                </div>
            </td>
            <td width="50%" style="padding-left: 10px;">
                <div class="stat-box">
                    <div class="stat-value"><?= number_format($uniqueScans) ?></div>
                    <div class="stat-label">Unique Visitors</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Main Chart -->
    <div class="card">
        <div class="card-title">Activity Trend</div>
        <img src="<?= $chartUrl ?>" class="chart-img">
    </div>

    <table class="grid" style="margin-top: 30px;">
        <tr>
            <!-- Countries -->
            <td width="50%" style="padding-right: 15px;">
                <div class="card">
                    <div class="card-title">Top Countries</div>
                    <table>
                        <thead>
                            <tr><th>Country</th><th>Scans</th></tr>
                        </thead>
                        <tbody>
                            <?php if (empty($countries)): ?>
                                <tr><td colspan="2" style="text-align:center; color:#94a3b8;">No data available</td></tr>
                            <?php else: ?>
                                <?php foreach ($countries as $c): ?>
                                <tr>
                                    <td><?= Html::encode($c['country']) ?></td>
                                    <td><strong><?= number_format($c['count']) ?></strong></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </td>
            <!-- Devices -->
            <td width="50%" style="padding-left: 15px;">
                <div class="card">
                    <div class="card-title">Device Distribution</div>
                    <table>
                        <thead>
                            <tr><th>Device</th><th>Scans</th></tr>
                        </thead>
                        <tbody>
                            <?php if (empty($devices)): ?>
                                <tr><td colspan="2" style="text-align:center; color:#94a3b8;">No data available</td></tr>
                            <?php else: ?>
                                <?php foreach ($devices as $d): ?>
                                <tr>
                                    <td><span class="badge"><?= Html::encode(ucfirst($d['device_type'])) ?></span></td>
                                    <td><strong><?= number_format($d['count']) ?></strong></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <!-- Links Summary -->
    <div class="card">
        <div class="card-title">Included Links</div>
        <table>
            <thead>
                <tr>
                    <th style="text-align:left;">Title / Code</th>
                    <th style="text-align:right;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($links as $link): ?>
                <tr>
                    <td>
                        <div style="font-weight:bold;"><?= Html::encode($link->title ?: 'No Title') ?></div>
                        <div style="font-size:11px; color:#64748b;"><?= Html::encode($link->short_code) ?></div>
                    </td>
                    <td style="text-align:right;">
                        <span class="badge" style="background:#f1f5f9; color:#475569;"><?= $link->getStatusLabel() ?></span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="footer">
    &copy; <?= date('Y') ?> UrlShortener Pro — Professional Statistics Export. Page 1 of 1
</div>

</body>
</html>
