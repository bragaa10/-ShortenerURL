<?php

use yii\helpers\Html;

use Yii;

/** @var app\models\User $model */

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => 'Utilizadores', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="page-header">
    <h1><i class="bi bi-person-fill"></i> <?= Html::encode($this->title) ?></h1>
    <div style="display:flex; gap:8px; flex-wrap:wrap;">
        <?= Html::a('<i class="bi bi-pencil"></i> Editar', ['update', 'id' => $model->id], ['class' => 'btn btn-secondary']) ?>
        <?php if ($model->id !== Yii::$app->user->id): ?>
            <?= Html::a(
                $model->status == 10
                    ? '<i class="bi bi-person-slash"></i> Desativar'
                    : '<i class="bi bi-person-check"></i> Ativar',
                ['toggle-status', 'id' => $model->id],
                [
                    'class' => $model->status == 10 ? 'btn btn-danger' : 'btn btn-success',
                    'data-method' => 'post',
                    'data-confirm' => $model->status == 10
                        ? 'Tem a certeza que deseja desativar este utilizador?'
                        : 'Tem a certeza que deseja ativar este utilizador?',
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
    <div class="data-card-header"><h3>Detalhes do Utilizador</h3></div>
    <div class="data-card-body">
        <table class="table detail-view">
            <tr><th>ID</th><td><?= $model->id ?></td></tr>
            <tr><th>Nome</th><td><?= Html::encode($model->username) ?></td></tr>
            <tr><th>Email</th><td><?= Html::encode($model->email) ?></td></tr>
            <tr>
                <th>Perfil</th>
                <td><span class="badge <?= $model->role === 'admin' ? 'badge-warning' : 'badge-info' ?>"><?= $model->getRoleLabel() ?></span></td>
            </tr>
            <tr>
                <th>Estado</th>
                <td><span class="badge <?= $model->status == 10 ? 'badge-success' : 'badge-danger' ?>"><?= $model->getStatusLabel() ?></span></td>
            </tr>
            <tr><th>Criado Em</th><td><?= date('d/m/Y H:i', $model->created_at) ?></td></tr>
            <tr><th>Último Login</th><td><?= $model->last_login_at ? date('d/m/Y H:i', $model->last_login_at) : '—' ?></td></tr>
            <tr><th>Total de Links</th><td><strong><?= count($model->shortUrls) ?></strong></td></tr>
            <tr><th>Total de Campanhas</th><td><strong><?= count($model->campaigns) ?></strong></td></tr>
        </table>
    </div>
</div>
