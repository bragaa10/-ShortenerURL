<?php

/** @var yii\web\View $this */
/** @var app\models\ProfileForm $model */
/** @var app\models\User $user */

use yii\widgets\ActiveForm;
use yii\helpers\Html;

$this->title = 'My Profile';
?>

<div class="page-header">
    <h1><i class="bi bi-person-circle"></i> Profile</h1>
</div>

<!-- Profile stats strip -->
<div class="stat-cards" style="margin-bottom: 24px;">
    <div class="stat-card">
        <div class="stat-icon primary"><i class="bi bi-link-45deg"></i></div>
        <div class="stat-value"><?= count($user->shortUrls) ?></div>
        <div class="stat-label">Links Created</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon secondary"><i class="bi bi-megaphone-fill"></i></div>
        <div class="stat-value"><?= count($user->campaigns) ?></div>
        <div class="stat-label">Campaigns</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon success"><i class="bi bi-person-badge"></i></div>
        <div class="stat-value">
            <span class="badge <?= $user->role === 'admin' ? 'badge-warning' : 'badge-info' ?>">
                <?= $user->getRoleLabel() ?>
            </span>
        </div>
        <div class="stat-label">Access Role</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon danger"><i class="bi bi-calendar-check"></i></div>
        <div class="stat-value" style="font-size: 16px;"><?= date('d/m/Y', $user->created_at) ?></div>
        <div class="stat-label">Member since</div>
    </div>
</div>

<?php $form = ActiveForm::begin(['id' => 'profile-form']); ?>

<div class="grid-2">
    <!-- Personal Info -->
    <div class="data-card">
        <div class="data-card-header">
            <h3><i class="bi bi-person-fill"></i> Personal Information</h3>
        </div>
        <div class="data-card-body">
            <?= $form->field($model, 'username')->textInput(['placeholder' => 'Your name']) ?>
            <?= $form->field($model, 'email')->textInput(['placeholder' => 'your@email.com']) ?>
            <?= $form->field($model, 'profile_company')->textInput(['placeholder' => 'Company (optional)']) ?>
            <?= $form->field($model, 'profile_website')->textInput(['placeholder' => 'https://yoursite.com (optional)']) ?>
            <?= $form->field($model, 'profile_bio')->textarea(['rows' => 3, 'placeholder' => 'A brief bio about you (optional)']) ?>
        </div>
    </div>

    <!-- Change Password -->
    <div class="data-card">
        <div class="data-card-header">
            <h3><i class="bi bi-shield-lock-fill"></i> Change Password</h3>
        </div>
        <div class="data-card-body">
            <p style="color: var(--text-secondary); font-size: 13px; margin-bottom: 20px;">
                Leave password fields blank if you don't want to change.
            </p>

            <?= $form->field($model, 'current_password')->passwordInput(['placeholder' => 'Current password']) ?>
            <?= $form->field($model, 'new_password')->passwordInput(['placeholder' => 'New password (min. 6 characters)']) ?>
            <?= $form->field($model, 'new_password_confirm')->passwordInput(['placeholder' => 'Confirm new password']) ?>

            <div style="padding: 14px; background: rgba(99,102,241,0.08); border-radius: 8px; margin-top: 8px;">
                <p style="font-size: 13px; color: var(--text-secondary); margin: 0;">
                    <i class="bi bi-info-circle" style="color: var(--accent-primary);"></i>
                    <strong style="color: var(--text-primary);">Last session:</strong>
                    <?= $user->last_login_at ? date('d/m/Y H:i', $user->last_login_at) : 'This is your first session' ?>
                </p>
            </div>
        </div>
    </div>
</div>

<div class="data-card">
    <div class="data-card-body flex-resp">
        <?= Html::submitButton('<i class="bi bi-check-lg"></i> Save Changes', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="bi bi-arrow-left"></i> Dashboard', ['/dashboard/index'], ['class' => 'btn btn-secondary']) ?>
        <span style="flex:1;"></span>
        <a href="<?= Yii::$app->urlManager->createUrl(['/site/privacy']) ?>" style="font-size: 13px; color: var(--text-muted);">
            <i class="bi bi-shield-check"></i> Privacy Policy
        </a>
    </div>
</div>

<?php ActiveForm::end(); ?>
