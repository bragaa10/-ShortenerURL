<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\ShortUrl $model */

$this->title = $model->title ?: $model->short_code;
$this->params['breadcrumbs'][] = ['label' => 'Links', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Always enable QR — JS renders it from the short URL
$shortUrlForQr = $model->getShortUrl();
if (strpos($shortUrlForQr, '?') === false) {
    $shortUrlForQr .= '?source=qr';
} else {
    $shortUrlForQr .= '&source=qr';
}
$downloadMode  = Yii::$app->request->get('download', '');
?>

<div class="page-header">
    <h1><i class="bi bi-link-45deg"></i> <?= Html::encode($this->title) ?></h1>
    <div>
        <?= Html::a('<i class="bi bi-bar-chart-fill"></i> Estatísticas', ['stats', 'id' => $model->id], ['class' => 'btn btn-outline-primary']) ?>
        <?= Html::a('<i class="bi bi-pencil"></i> Editar', ['update', 'id' => $model->id], ['class' => 'btn btn-secondary']) ?>
        <?= Html::a('<i class="bi bi-trash"></i> Eliminar', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data'  => ['confirm' => 'Tem a certeza?', 'method' => 'post'],
        ]) ?>
    </div>
</div>

<div class="grid-2">
    <!-- Link Details -->
    <div class="data-card">
        <div class="data-card-header">
            <h3>Detalhes do Link</h3>
        </div>
        <div class="data-card-body">
            <table class="table detail-view">
                <tr>
                    <th>URL Curto</th>
                    <td>
                        <div class="short-url-display">
                            <code id="shortUrl"><?= Html::encode($model->getShortUrl()) ?></code>
                            <button class="btn-copy" onclick="copyUrl()" title="Copiar">
                                <i class="bi bi-clipboard"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>URL Original</th>
                    <td>
                        <a href="<?= Html::encode($model->original_url) ?>" target="_blank" style="word-break: break-all;">
                            <?= Html::encode($model->original_url) ?>
                            <i class="bi bi-box-arrow-up-right" style="font-size:11px;"></i>
                        </a>
                    </td>
                </tr>
                <tr>
                    <th>Estado</th>
                    <td><span class="badge <?= $model->getStatusBadgeClass() ?>"><?= $model->getStatusLabel() ?></span></td>
                </tr>
                <tr>
                    <th>Campanha</th>
                    <td><?= $model->campaign ? Html::encode($model->campaign->name) : '<span style="color:var(--text-muted)">—</span>' ?></td>
                </tr>
                <tr>
                    <th>Total Scans</th>
                    <td><strong><?= number_format($model->getTotalScans()) ?></strong> (<?= number_format($model->getUniqueScans()) ?> únicos)</td>
                </tr>
                <tr>
                    <th>Expira Em</th>
                    <td><?= $model->expires_at ? date('d/m/Y H:i', $model->expires_at) : '<span style="color:var(--text-muted)">Nunca</span>' ?></td>
                </tr>
                <tr>
                    <th>Criado Em</th>
                    <td><?= date('d/m/Y H:i', $model->created_at) ?></td>
                </tr>
                <?php if ($model->notes): ?>
                <tr>
                    <th>Notas</th>
                    <td><?= Html::encode($model->notes) ?></td>
                </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>

    <!-- QR Code (rendered 100% client-side) -->
    <div class="data-card">
        <div class="data-card-header">
            <h3>QR Code</h3>
            <div>
                <button onclick="downloadQR('png')" class="btn btn-sm btn-secondary" style="margin-right:4px;" id="btn-dl-png">
                    <i class="bi bi-download"></i> PNG
                </button>
                <button onclick="downloadQR('svg')" class="btn btn-sm btn-secondary">
                    <i class="bi bi-filetype-svg"></i> SVG
                </button>
            </div>
        </div>
        <div class="data-card-body" style="text-align: center; padding: 24px;">
            <div id="qrcode-container" style="display:inline-block; padding:16px; background:#fff; border-radius:8px; box-shadow: 0 2px 12px rgba(0,0,0,0.15);">
                <div id="qrcode"></div>
            </div>
            <p style="margin-top:14px; font-size:12px; color:var(--text-muted);">
                <i class="bi bi-info-circle"></i>
                Aponte a câmara para o código QR para aceder ao link curto.
            </p>
        </div>
    </div>
</div>

<!-- Load qrcode.js library from CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
var QR_URL = <?= json_encode($shortUrlForQr) ?>;
var SHORT_CODE = <?= json_encode($model->short_code) ?>;
var DOWNLOAD_MODE = <?= json_encode($downloadMode) ?>;
var qrInstance = null;

// Generate QR Code on page load
document.addEventListener('DOMContentLoaded', function () {
    qrInstance = new QRCode(document.getElementById('qrcode'), {
        text: QR_URL,
        width: 220,
        height: 220,
        colorDark: '#000000',
        colorLight: '#ffffff',
        correctLevel: QRCode.CorrectLevel.H
    });

    // Auto-trigger download if requested via URL param
    if (DOWNLOAD_MODE === 'png' || DOWNLOAD_MODE === 'svg') {
        setTimeout(function() { downloadQR(DOWNLOAD_MODE); }, 800);
    }
});

function getQrCanvas() {
    return document.querySelector('#qrcode canvas');
}

function downloadQR(format) {
    var canvas = getQrCanvas();
    if (!canvas) {
        alert('QR Code ainda a carregar. Tente novamente em instantes.');
        return;
    }

    var filename = 'qrcode-' + SHORT_CODE;

    if (format === 'svg') {
        // Export as SVG string by reconstructing from canvas pixels
        var size = canvas.width;
        var ctx = canvas.getContext('2d');
        var imgData = ctx.getImageData(0, 0, size, size);
        var cellSize = 1;
        var svgParts = ['<svg xmlns="http://www.w3.org/2000/svg" width="' + size + '" height="' + size + '" viewBox="0 0 ' + size + ' ' + size + '">'];
        svgParts.push('<rect width="' + size + '" height="' + size + '" fill="#fff"/>');
        for (var y = 0; y < size; y++) {
            for (var x = 0; x < size; x++) {
                var idx = (y * size + x) * 4;
                if (imgData.data[idx] < 128) { // dark pixel
                    svgParts.push('<rect x="' + x + '" y="' + y + '" width="1" height="1" fill="#000"/>');
                }
            }
        }
        svgParts.push('</svg>');
        var svgBlob = new Blob([svgParts.join('')], { type: 'image/svg+xml' });
        triggerDownload(URL.createObjectURL(svgBlob), filename + '.svg');
    } else {
        // Export as PNG via canvas
        var dataUrl = canvas.toDataURL('image/png');
        triggerDownload(dataUrl, filename + '.png');
    }
}

function triggerDownload(href, filename) {
    var a = document.createElement('a');
    a.href = href;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}

function copyUrl() {
    var url = document.getElementById('shortUrl').textContent;
    navigator.clipboard.writeText(url).then(function () {
        var btn = document.querySelector('.btn-copy i');
        btn.className = 'bi bi-check-lg';
        btn.style.color = 'var(--accent-success)';
        setTimeout(function() {
            btn.className = 'bi bi-clipboard';
            btn.style.color = '';
        }, 2000);
    });
}
</script>
