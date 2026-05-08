<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\UserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * UserController implements the CRUD actions for User model.
 * Admin-only access.
 */
class UserController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            return Yii::$app->user->identity->isAdmin();
                        },
                    ],
                ],
                'denyCallback' => function () {
                    throw new ForbiddenHttpException('Acesso restrito a administradores.');
                },
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'toggle-status' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all User models.
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new User model (admin only).
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new User();
        $model->status = User::STATUS_ACTIVE;
        $model->role = User::ROLE_USER;
        // Virtual attribute for password
        $plainPassword = '';

        if ($this->request->isPost && $model->load($this->request->post())) {
            $plainPassword = $this->request->post('plain_password', '');
            if (empty($plainPassword) || strlen($plainPassword) < 6) {
                $model->addError('password_hash', 'A password deve ter pelo menos 6 caracteres.');
            } else {
                $model->setPassword($plainPassword);
                $model->generateAuthKey();
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Utilizador criado com sucesso.');
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Toggles the active/inactive status of a user.
     * @param int $id
     * @return \yii\web\Response
     */
    public function actionToggleStatus($id)
    {
        $model = $this->findModel($id);

        // Prevent admin from deactivating their own account
        if ($model->id === Yii::$app->user->id) {
            Yii::$app->session->setFlash('error', 'Não pode desativar a sua própria conta.');
            return $this->redirect(['view', 'id' => $id]);
        }

        $model->status = ($model->status == User::STATUS_ACTIVE)
            ? User::STATUS_INACTIVE
            : User::STATUS_ACTIVE;
        $model->save(false, ['status']);

        $label = $model->status == User::STATUS_ACTIVE ? 'ativado' : 'desativado';
        Yii::$app->session->setFlash('success', "Utilizador {$label} com sucesso.");
        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * Displays a single User model.
     * @param int $id ID
     * @return string
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Updates an existing User model.
     * @param int $id ID
     * @return string|\yii\web\Response
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Utilizador atualizado.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the User model.
     * @param int $id ID
     * @return User
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = User::findOne(['id' => $id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('O utilizador não existe.');
    }
}
