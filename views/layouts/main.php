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
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => '/favicon.ico?v=1']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/png', 'href' => '/favicon.ico?v=1']);
$this->registerLinkTag(['rel' => 'preconnect', 'href' => 'https://fonts.googleapis.com']);
$this->registerLinkTag(['rel' => 'stylesheet', 'href' => 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap']);
$this->registerLinkTag(['rel' => 'stylesheet', 'href' => 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css']);
$this->registerCssFile('@web/css/chatbot.css');
$this->registerJsFile('@web/js/chatbot.js', ['depends' => [\yii\web\JqueryAsset::class]]);

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
    <?= $this->render('_sidebar', [
        'user' => $user,
        'currentController' => $currentController,
        'currentAction' => $currentAction,
    ]) ?>

    <!-- Main Content -->
    <div class="app-main">
        <!-- Top Bar -->
        <header class="app-topbar">
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>

            <div class="topbar-right">
                <div class="topbar-user">
                    <span class="d-none d-md-inline"><?= Html::encode($user->username ?? '') ?></span>
                    <?= Html::beginForm(['/site/logout'], 'post') ?>
                    <?= Html::submitButton(
                        '<i class="bi bi-box-arrow-right"></i> Logout',
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const body = document.body;

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            if (window.innerWidth < 992) {
                body.classList.toggle('sidebar-open');
            } else {
                body.classList.toggle('sidebar-collapsed');
            }
        });
    }

    // Close sidebar on mobile when clicking outside
    document.addEventListener('click', function(event) {
        if (window.innerWidth < 992 && body.classList.contains('sidebar-open')) {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.getElementById('sidebarToggle');
            if (sidebar && !sidebar.contains(event.target) && toggle && !toggle.contains(event.target)) {
                body.classList.remove('sidebar-open');
            }
        }
    });
});
</script>

<?= $this->render('_chatbot') ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
