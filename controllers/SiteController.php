<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\RegisterForm;
use app\models\ForgotPasswordForm;
use app\models\ResetPasswordForm;
use app\models\ProfileForm;
use yii\base\InvalidArgumentException;

/**
 * SiteController handles authentication, profile, and public pages.
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout', 'profile'],
                'rules' => [
                    [
                        'actions' => ['logout', 'profile'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage. Redirects authenticated users to dashboard.
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['/dashboard/index']);
        }
        return $this->redirect(['/site/login']);
    }

    /**
     * Login action.
     */
    public function actionLogin()
    {
        $this->layout = 'auth';

        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['/dashboard/index']);
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect(['/dashboard/index']);
        }

        $model->password = '';
        return $this->render('login', ['model' => $model]);
    }

    /**
     * Register action.
     */
    public function actionRegister()
    {
        $this->layout = 'auth';

        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['/dashboard/index']);
        }

        $model = new RegisterForm();
        if ($model->load(Yii::$app->request->post())) {
            $user = $model->register();
            if ($user !== null) {
                Yii::$app->user->login($user);
                Yii::$app->session->setFlash('success', 'Conta criada com sucesso! Bem-vindo.');
                return $this->redirect(['/dashboard/index']);
            }
        }

        return $this->render('register', ['model' => $model]);
    }

    /**
     * Forgot password action — generates reset token.
     */
    public function actionForgotPassword()
    {
        $this->layout = 'auth';

        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['/dashboard/index']);
        }

        $model = new ForgotPasswordForm();
        $resetLink = null;

        if ($model->load(Yii::$app->request->post()) && $model->sendResetLink()) {
            // In dev mode: show the reset link directly on screen
            $token = Yii::$app->session->get('_dev_reset_token');
            if ($token) {
                $resetLink = Yii::$app->urlManager->createAbsoluteUrl(['/site/reset-password', 'token' => $token]);
            }
            Yii::$app->session->setFlash('success', 'Se o email existir, receberá um link de redefinição.');
        }

        return $this->render('forgot-password', [
            'model' => $model,
            'resetLink' => $resetLink,
        ]);
    }

    /**
     * Reset password action — validates token and sets new password.
     *
     * @param string $token
     */
    public function actionResetPassword($token)
    {
        $this->layout = 'auth';

        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(['/site/forgot-password']);
        }

        if ($model->load(Yii::$app->request->post()) && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'Password redefinida com sucesso! Pode fazer login agora.');
            return $this->redirect(['/site/login']);
        }

        return $this->render('reset-password', ['model' => $model]);
    }

    /**
     * User profile management.
     */
    public function actionProfile()
    {
        $user = Yii::$app->user->identity;
        $model = new ProfileForm($user);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Perfil atualizado com sucesso.');
            return $this->refresh();
        }

        return $this->render('profile', ['model' => $model, 'user' => $user]);
    }

    /**
     * Privacy policy page (RGPD compliance).
     */
    public function actionPrivacy()
    {
        return $this->render('privacy');
    }

    /**
     * Logout action.
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->redirect(['/site/login']);
    }
}
