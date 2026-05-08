<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Html;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);
$this->registerLinkTag(['rel' => 'preconnect', 'href' => 'https://fonts.googleapis.com']);
$this->registerLinkTag(['rel' => 'stylesheet', 'href' => 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap']);
$this->registerLinkTag(['rel' => 'stylesheet', 'href' => 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css']);

$currentController = Yii::$app->controller->id;
$currentAction = Yii::$app->controller->action->id;
$user = Yii::$app->user->identity;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?> | <?= Yii::$app->name ?></title>
    <?php $this->head() ?>
</head>
<body class="app-body">
<?php $this->beginBody() ?>

<div class="app-wrapper">
    <!-- Sidebar -->
    <aside class="app-sidebar" id="sidebar">
        <div class="sidebar-brand">
            <i class="bi bi-link-45deg"></i>
            <span>Encurtador</span>
        </div>

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
                <span>Campanhas</span>
            </a>

            <a href="<?= Yii::$app->urlManager->createUrl(['/scanlog/index']) ?>"
               class="sidebar-link <?= $currentController === 'scanlog' ? 'active' : '' ?>">
                <i class="bi bi-bar-chart-fill"></i>
                <span>Scan Logs</span>
            </a>

            <?php if ($user && $user->isAdmin()): ?>
            <div class="sidebar-divider"></div>
            <div class="sidebar-heading">Admin</div>

            <a href="<?= Yii::$app->urlManager->createUrl(['/user/index']) ?>"
               class="sidebar-link <?= $currentController === 'user' ? 'active' : '' ?>">
                <i class="bi bi-people-fill"></i>
                <span>Utilizadores</span>
            </a>
            <?php endif; ?>

            <div class="sidebar-divider"></div>

            <a href="<?= Yii::$app->urlManager->createUrl(['/site/profile']) ?>"
               class="sidebar-link <?= $currentController === 'site' && $currentAction === 'profile' ? 'active' : '' ?>">
                <i class="bi bi-person-circle"></i>
                <span>Perfil</span>
            </a>

            <a href="<?= Yii::$app->urlManager->createUrl(['/site/privacy']) ?>"
               class="sidebar-link">
                <i class="bi bi-shield-check"></i>
                <span>Privacidade</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="user-avatar">
                    <?= strtoupper(substr($user->username ?? 'U', 0, 1)) ?>
                </div>
                <div class="user-info">
                    <div class="user-name"><?= Html::encode($user->username ?? '') ?></div>
                    <div class="user-role"><?= $user ? $user->getRoleLabel() : '' ?></div>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="app-main">
        <!-- Top Bar -->
        <header class="app-topbar">
            <button class="sidebar-toggle" onclick="document.body.classList.toggle('sidebar-collapsed')">
                <i class="bi bi-list"></i>
            </button>

            <div class="topbar-right">
                <div class="topbar-user">
                    <span class="d-none d-md-inline"><?= Html::encode($user->username ?? '') ?></span>
                    <?= Html::beginForm(['/site/logout'], 'post') ?>
                    <?= Html::submitButton(
                        '<i class="bi bi-box-arrow-right"></i> Sair',
                        ['class' => 'btn-logout']
                    ) ?>
                    <?= Html::endForm() ?>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="app-content">
            <?= Alert::widget() ?>
            <?= $content ?>
        </main>
    </div>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
