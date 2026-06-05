<?php
use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var app\models\User $user */
/** @var string $currentController */
/** @var string $currentAction */
?>
<aside class="app-sidebar" id="sidebar">
    <a href="<?= Yii::$app->urlManager->createUrl(['/dashboard/index']) ?>" class="sidebar-brand" style="text-decoration: none;">
        <i class="bi bi-link-45deg"></i>
        <span>Shortener</span>
    </a>

    <nav class="sidebar-nav">
        <a href="<?= Yii::$app->urlManager->createUrl(['/dashboard/index']) ?>"
           class="sidebar-link <?= $currentController === 'dashboard' ? 'active' : '' ?>">
            <i class="bi bi-grid-1x2-fill"></i>
            <span>Dashboard</span>
        </a>

        <a href="<?= Yii::$app->urlManager->createUrl(['/short-url/index']) ?>"
           class="sidebar-link <?= $currentController === 'short-url' ? 'active' : '' ?>">
            <i class="bi bi-link-45deg"></i>
            <span>Links</span>
        </a>

        <a href="<?= Yii::$app->urlManager->createUrl(['/campaign/index']) ?>"
           class="sidebar-link <?= $currentController === 'campaign' ? 'active' : '' ?>">
            <i class="bi bi-megaphone-fill"></i>
            <span>Campaigns</span>
        </a>

        <a href="<?= Yii::$app->urlManager->createUrl(['/scanlog/index']) ?>"
           class="sidebar-link <?= $currentController === 'scanlog' ? 'active' : '' ?>">
            <i class="bi bi-bar-chart-fill"></i>
            <span>Scan Logs</span>
        </a>

        <a href="<?= Yii::$app->urlManager->createUrl(['/report/index']) ?>"
           class="sidebar-link <?= $currentController === 'report' ? 'active' : '' ?>">
            <i class="bi bi-file-earmark-pdf-fill"></i>
            <span>Reports</span>
        </a>

        <?php if ($user && $user->isAdmin()): ?>
        <div class="sidebar-divider"></div>
        <div class="sidebar-heading">Admin</div>

        <a href="<?= Yii::$app->urlManager->createUrl(['/user/index']) ?>"
           class="sidebar-link <?= $currentController === 'user' ? 'active' : '' ?>">
            <i class="bi bi-people-fill"></i>
            <span>Users</span>
        </a>
        <?php endif; ?>

        <div class="sidebar-divider"></div>

        <a href="<?= Yii::$app->urlManager->createUrl(['/site/profile']) ?>"
           class="sidebar-link <?= $currentController === 'site' && $currentAction === 'profile' ? 'active' : '' ?>">
            <i class="bi bi-person-circle"></i>
            <span>Profile</span>
        </a>

        <a href="<?= Yii::$app->urlManager->createUrl(['/site/privacy']) ?>"
           class="sidebar-link">
            <i class="bi bi-shield-check"></i>
            <span>Privacy</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="user-avatar">
                <?= Html::encode(strtoupper(substr($user->username ?? 'U', 0, 1))) ?>
            </div>
            <div class="user-info">
                <div class="user-name"><?= Html::encode($user->username ?? '') ?></div>
                <div class="user-role"><?= $user ? $user->getRoleLabel() : '' ?></div>
            </div>
        </div>
    </div>
</aside>
