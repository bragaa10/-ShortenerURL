<?php

use yii\helpers\Html;
use Yii;

/** @var app\models\User $model */

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="page-header">
    <h1><i class="bi bi-person-fill"></i> <?= Html::encode($this->title) ?></h1>
    <div style="display:flex; gap:8px; flex-wrap:wrap;">
        <?= Html::a('<i class="bi bi-pencil"></i> Edit', ['update', 'id' => $model->id], ['class' => 'btn btn-secondary']) ?>
        <?php if ($model->id !== Yii::$app->user->id): ?>
            <?= Html::a(
                $model->status == 10
                    ? '<i class="bi bi-person-slash"></i> Deactivate'
                    : '<i class="bi bi-person-check"></i> Activate',
                ['toggle-status', 'id' => $model->id],
                [
                    'class' => $model->status == 10 ? 'btn btn-danger' : 'btn btn-success',
                    'data-method' => 'post',
                    'data-confirm' => $model->status == 10
                        ? 'Are you sure you want to deactivate this user?'
                        : 'Are you sure you want to activate this user?',
                ]
            ) ?>
        <?php endif; ?>
    </div>
</div>

<?php if (Yii::$app->session->hasFlash('success')): ?>
    <div class="alert alert-success"><?= Yii::$app->session->getFlash('success') ?></div>
<?php endif; ?>
<?php if (Yii::$app->session->hasFlash('error')): ?>
    <div class="alert alert-danger"><?= Yii::$app->session->getFlash('error') ?></div>
<?php endif; ?>

<div class="data-card">
    <div class="data-card-header"><h3>User Details</h3></div>
    <div class="data-card-body">
        <table class="table detail-view">
            <tr><th>ID</th><td><?= $model->id ?></td></tr>
            <tr><th>Name</th><td><?= Html::encode($model->username) ?></td></tr>
            <tr><th>Email</th><td><?= Html::encode($model->email) ?></td></tr>
            <tr>
                <th>Profile</th>
                <td><span class="badge <?= $model->role === 'admin' ? 'badge-warning' : 'badge-info' ?>"><?= $model->getRoleLabel() ?></span></td>
            </tr>
            <tr>
                <th>Status</th>
                <td><span class="badge <?= $model->status == 10 ? 'badge-success' : 'badge-danger' ?>"><?= $model->getStatusLabel() ?></span></td>
            </tr>
            <tr><th>Created At</th><td><?= date('d/m/Y H:i', $model->created_at) ?></td></tr>
            <tr><th>Last Login</th><td><?= $model->last_login_at ? date('d/m/Y H:i', $model->last_login_at) : '—' ?></td></tr>
            <tr><th>Total Links</th><td><strong><?= count($model->shortUrls) ?></strong></td></tr>
            <tr><th>Total Campaigns</th><td><strong><?= count($model->campaigns) ?></strong></td></tr>
        </table>
    </div>
</div>
