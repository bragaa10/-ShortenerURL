<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\base\InvalidArgumentException;

/**
 * ResetPasswordForm handles the password reset process.
 */
class ResetPasswordForm extends Model
{
    public $password;
    public $password_confirm;

    /** @var User */
    private $_user;

    /**
     * Creates a new ResetPasswordForm from a valid token.
     *
     * @param string $token
     * @throws InvalidArgumentException if the token is invalid or expired
     */
    public function __construct($token, $config = [])
    {
        if (empty($token) || !is_string($token)) {
            throw new InvalidArgumentException('Token de redefinição de password inválido.');
        }

        $this->_user = static::findByToken($token);
        if (!$this->_user) {
            throw new InvalidArgumentException('Token de redefinição de password inválido ou expirado.');
        }

        parent::__construct($config);
    }

    /**
     * Finds user by password reset token.
     * Token expires after 1 hour.
     *
     * @param string $token
     * @return User|null
     */
    public static function findByToken($token)
    {
        if (empty($token)) {
            return null;
        }

        $user = User::findOne(['password_reset_token' => $token]);
        if (!$user) {
            return null;
        }

        // Extract timestamp from token (format: randomstring_timestamp)
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);

        // Token valid for 1 hour
        if ($timestamp < (time() - 3600)) {
            return null;
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['password', 'password_confirm'], 'required'],
            ['password', 'string', 'min' => 6],
            ['password_confirm', 'compare', 'compareAttribute' => 'password', 'message' => 'As passwords não coincidem.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'password' => 'Nova Password',
            'password_confirm' => 'Confirmar Password',
        ];
    }

    /**
     * Resets the password and invalidates the token.
     *
     * @return bool whether the password was reset successfully
     */
    public function resetPassword()
    {
        if (!$this->validate()) {
            return false;
        }

        $user = $this->_user;
        $user->setPassword($this->password);
        $user->password_reset_token = null;
        $user->generateAuthKey();

        return $user->save(false, ['password_hash', 'password_reset_token', 'auth_key']);
    }
}
