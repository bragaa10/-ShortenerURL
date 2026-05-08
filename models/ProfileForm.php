<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ProfileForm handles user profile updates.
 * Separates profile editing from password changes for better UX.
 */
class ProfileForm extends Model
{
    public $username;
    public $email;
    public $profile_bio;
    public $profile_company;
    public $profile_website;
    public $current_password;
    public $new_password;
    public $new_password_confirm;

    /** @var User */
    private $_user;

    /**
     * @param User $user
     * @param array $config
     */
    public function __construct(User $user, $config = [])
    {
        $this->_user = $user;
        $this->username = $user->username;
        $this->email = $user->email;
        $this->profile_bio = $user->profile_bio ?? null;
        $this->profile_company = $user->profile_company ?? null;
        $this->profile_website = $user->profile_website ?? null;
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'email'], 'required'],
            ['username', 'string', 'min' => 3, 'max' => 150],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => User::class,
                'filter' => ['!=', 'id', $this->_user->id],
                'message' => 'Este email já está em uso por outro utilizador.',
            ],
            ['username', 'unique', 'targetClass' => User::class,
                'filter' => ['!=', 'id', $this->_user->id],
                'message' => 'Este nome de utilizador já existe.',
            ],
            ['profile_bio', 'string'],
            ['profile_company', 'string', 'max' => 150],
            ['profile_website', 'url', 'defaultScheme' => 'https'],
            ['current_password', 'validateCurrentPassword'],
            ['new_password', 'string', 'min' => 6],
            ['new_password_confirm', 'compare', 'compareAttribute' => 'new_password', 'message' => 'As passwords não coincidem.'],
            [['profile_bio', 'profile_company', 'profile_website', 'current_password', 'new_password', 'new_password_confirm'], 'default', 'value' => null],
        ];
    }

    /**
     * Validates current password only if user is trying to change it.
     */
    public function validateCurrentPassword($attribute, $params)
    {
        if (!empty($this->new_password)) {
            if (empty($this->current_password)) {
                $this->addError($attribute, 'Para alterar a password, introduza a sua password atual.');
            } elseif (!$this->_user->validatePassword($this->current_password)) {
                $this->addError($attribute, 'A password atual está incorreta.');
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'username' => 'Nome',
            'email' => 'Email',
            'profile_bio' => 'Biografia',
            'profile_company' => 'Empresa',
            'profile_website' => 'Website',
            'current_password' => 'Password Atual',
            'new_password' => 'Nova Password',
            'new_password_confirm' => 'Confirmar Nova Password',
        ];
    }

    /**
     * Saves the profile changes.
     *
     * @return bool
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $user = $this->_user;
        $user->username = $this->username;
        $user->email = $this->email;

        // Only set profile fields if they exist in the DB
        if (isset($user->profile_bio)) {
            $user->profile_bio = $this->profile_bio;
        }
        if (isset($user->profile_company)) {
            $user->profile_company = $this->profile_company;
        }
        if (isset($user->profile_website)) {
            $user->profile_website = $this->profile_website;
        }

        // Change password if requested
        if (!empty($this->new_password)) {
            $user->setPassword($this->new_password);
            $user->generateAuthKey();
        }

        return $user->save(false);
    }
}
