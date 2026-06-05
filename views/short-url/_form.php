<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Campaign;

/** @var yii\web\View $this */
/** @var app\models\ShortUrl $model */
/** @var yii\widgets\ActiveForm $form */
/** @var array $campaigns */
?>

<div class="data-card">
    <div class="data-card-body">

        <?php $form = ActiveForm::begin(['id' => 'short-url-form']); ?>

        <?= $form->field($model, 'original_url')->textInput([
            'placeholder' => 'https://example.com/very-long-page',
            'maxlength' => true,
        ])->label('Original URL *') ?>

        <?= $form->field($model, 'title')->textInput([
            'placeholder' => 'Ex: Summer Promotion',
            'maxlength' => true,
        ]) ?>

        <div class="form-row">
            <?= $form->field($model, 'short_code')->textInput([
                'placeholder' => 'Leave empty to generate automatically',
                'maxlength' => true,
            ])->hint('Custom code (optional)') ?>

            <?= $form->field($model, 'campaign_id')->dropDownList(
                $campaigns ?? [],
                ['prompt' => '— No campaign —']
            ) ?>
        </div>

        <div class="form-row">
            <?= $form->field($model, 'status')->dropDownList([
                1 => 'Active',
                0 => 'Inactive',
            ]) ?>

            <?= $form->field($model, 'expires_at')->textInput([
                'type' => 'date',
                'value' => $model->expires_at ? (is_numeric($model->expires_at) ? date('Y-m-d', $model->expires_at) : $model->expires_at) : '',
            ])->hint('Leave empty for no expiration') ?>
        </div>

        <?= $form->field($model, 'notes')->textarea([
            'rows' => 3,
            'placeholder' => 'Internal notes (optional)',
        ]) ?>

        <?= $form->field($model, 'tags')->textInput([
            'placeholder' => 'Ex: summer, promo, social-media (comma separated)',
        ]) ?>

        <!-- Password Protection -->
        <div class="data-card" style="margin-bottom: 0; border: 1px solid var(--border-color); background: var(--bg-secondary);">
            <div class="data-card-body" style="padding: 16px;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                    <input type="checkbox" id="pw_toggle"
                        name="ShortUrl[password_protected]"
                        value="1"
                        <?= $model->password_protected ? 'checked' : '' ?>
                        onchange="togglePasswordField(this)"
                        style="width:18px; height:18px; accent-color: var(--accent-primary); cursor:pointer;">
                    <label for="pw_toggle" style="margin:0; cursor:pointer; font-size:14px; color:var(--text-primary);">
                        <i class="bi bi-lock-fill" style="color:var(--accent-warning);"></i>
                        Protect this link with password
                    </label>
                </div>
                <p style="font-size:12px; color:var(--text-muted); margin: 0 0 12px 28px;">
                    Visitors will have to enter the password before being redirected.
                </p>
                <div id="pw_field" style="display: <?= $model->password_protected ? 'block' : 'none' ?>; margin-left: 28px;">
                    <?= $form->field($model, 'link_password')->passwordInput([
                        'placeholder' => $model->password_protected ? '(keep current password — leave empty)' : 'Minimum 4 characters',
                        'autocomplete' => 'new-password',
                    ])->label('Access Password') ?>
                </div>
            </div>
        </div>

        <div class="form-group" style="margin-top: 24px;">
            <?= Html::submitButton($model->isNewRecord ? '<i class="bi bi-plus-lg"></i> Create Link' : '<i class="bi bi-check-lg"></i> Save', ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-secondary', 'style' => 'margin-left: 8px;']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>

<?php
$js = <<<JS
// Password toggle
window.togglePasswordField = function(checkbox) {
    document.getElementById('pw_field').style.display = checkbox.checked ? 'block' : 'none';
};
JS;
$this->registerJs($js);
?>

<script>
function togglePasswordField(checkbox) {
    document.getElementById('pw_field').style.display = checkbox.checked ? 'block' : 'none';
}
</script>
