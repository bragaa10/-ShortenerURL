<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ForgotPasswordForm handles the "forgot password" request.
 * Generates a secure token and saves it to the user record.
 */
class ForgotPasswordForm extends Model
{
    public $email;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email'], 'required'],
            ['email', 'email'],
            ['email', 'validateEmailExists'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'email' => 'Email',
        ];
    }

    /**
     * Validates that a user exists with this email.
     */
    public function validateEmailExists($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = User::findByEmail($this->email);
            if (!$user) {
                $this->addError($attribute, 'Não existe nenhuma conta associada a este email.');
            }
        }
    }

    /**
     * Generates and saves a password reset token.
     *
     * @return bool whether the token was saved successfully
     */
    public function sendResetLink()
    {
        if (!$this->validate()) {
            return false;
        }

        $user = User::findByEmail($this->email);
        if (!$user) {
            return false;
        }

        // Generate secure token with expiry (1 hour): token_timestamp
        $token = Yii::$app->security->generateRandomString() . '_' . time();
        $user->password_reset_token = $token;

        if ($user->save(false, ['password_reset_token'])) {
            // In production: send email with reset link
            // For dev: store in session so user can access the link directly
            Yii::$app->session->set('_dev_reset_token', $token);
            Yii::$app->session->set('_dev_reset_email', $this->email);
            return true;
        }

        return false;
    }
}
