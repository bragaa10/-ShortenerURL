<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Generate Statistics Report';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="page-header">
    <h1><i class="bi bi-file-earmark-pdf-fill"></i> <?= Html::encode($this->title) ?></h1>
</div>

<div class="data-card">
    <div class="data-card-header">
        <h3>Report Configuration</h3>
    </div>
    <div class="data-card-body">
        <p class="text-secondary mb-4">Select the links you want to include in your professional PDF report. The report will include statistics from the last 30 days.</p>

        <?= Html::beginForm(['export'], 'post', ['id' => 'report-form']) ?>
            
            <div class="mb-4">
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="all_links" name="all_links" value="1" checked>
                    <label class="form-check-label" for="all_links"><strong>Include all my links</strong></label>
                </div>
            </div>

            <div id="link-selection-container" style="display: none; border: 1px solid var(--border-color); border-radius: 8px; padding: 20px; max-height: 400px; overflow-y: auto; background: rgba(0,0,0,0.02);">
                <label class="form-label mb-3">Select specific links:</label>
                <div class="row g-3">
                    <?php foreach ($links as $link): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="form-check">
                                <input class="form-check-input link-checkbox" type="checkbox" name="link_ids[]" value="<?= $link->id ?>" id="link_<?= $link->id ?>">
                                <label class="form-check-label" for="link_<?= $link->id ?>" style="font-size: 14px;">
                                    <?= Html::encode($link->title ?: $link->short_code) ?>
                                    <br><small class="text-muted"><?= $link->short_code ?></small>
                                </label>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="mt-4 pt-3 border-top">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-cloud-download"></i> Generate PDF Report
                </button>
            </div>

        <?= Html::endForm() ?>
    </div>
</div>

<?php
$js = <<<JS
$('#all_links').on('change', function() {
    if ($(this).is(':checked')) {
        $('#link-selection-container').slideUp();
    } else {
        $('#link-selection-container').slideDown();
    }
});

$('#report-form').on('submit', function(e) {
    if (!$('#all_links').is(':checked') && $('.link-checkbox:checked').length === 0) {
        alert('Please select at least one link or choose "Include all my links".');
        e.preventDefault();
    }
});
JS;
$this->registerJs($js);
?>
