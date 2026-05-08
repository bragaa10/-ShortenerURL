<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * RegisterForm is the model behind the registration form.
 */
class RegisterForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $password_confirm;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['username', 'email', 'password', 'password_confirm'], 'required'],
            ['username', 'string', 'min' => 3, 'max' => 150],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => User::class, 'message' => 'Este email já está registado.'],
            ['username', 'unique', 'targetClass' => User::class, 'message' => 'Este nome de utilizador já existe.'],
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
            'username' => 'Nome',
            'email' => 'Email',
            'password' => 'Password',
            'password_confirm' => 'Confirmar Password',
        ];
    }

    /**
     * Registers a new user.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function register()
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->status = User::STATUS_ACTIVE;
        $user->role = User::ROLE_USER;

        return $user->save() ? $user : null;
    }
}
