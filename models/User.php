<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $username
 * @property string $email
 * @property string $password_hash
 * @property string $auth_key
 * @property string|null $password_reset_token
 * @property int|null $status
 * @property string|null $role
 * @property string|null $profile_bio
 * @property string|null $profile_company
 * @property string|null $profile_website
 * @property int $created_at
 * @property int $updated_at
 * @property int|null $last_login_at
 *
 * @property Campaign[] $campaigns
 * @property ShortUrl[] $shortUrls
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 10;

    const ROLE_USER = 'user';
    const ROLE_ADMIN = 'admin';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['last_login_at', 'password_reset_token'], 'default', 'value' => null],
            [['status'], 'default', 'value' => self::STATUS_ACTIVE],
            [['role'], 'default', 'value' => self::ROLE_USER],
            [['username', 'email', 'password_hash'], 'required'],
            [['status', 'created_at', 'updated_at', 'last_login_at'], 'integer'],
            [['username'], 'string', 'max' => 150],
            [['email'], 'string', 'max' => 190],
            [['email'], 'email'],
            [['password_hash', 'auth_key', 'password_reset_token'], 'string', 'max' => 255],
            [['role'], 'string', 'max' => 50],
            [['role'], 'in', 'range' => [self::ROLE_USER, self::ROLE_ADMIN]],
            [['username'], 'unique'],
            [['email'], 'unique'],
            // Profile fields (optional — gracefully ignored if columns don't exist yet)
            [['profile_bio'], 'string'],
            [['profile_company'], 'string', 'max' => 150],
            [['profile_website'], 'url', 'defaultScheme' => 'https'],
            [['profile_bio', 'profile_company', 'profile_website'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Nome',
            'email' => 'Email',
            'password_hash' => 'Password',
            'auth_key' => 'Auth Key',
            'password_reset_token' => 'Token de Reset',
            'status' => 'Estado',
            'role' => 'Perfil',
            'profile_bio' => 'Biografia',
            'profile_company' => 'Empresa',
            'profile_website' => 'Website',
            'created_at' => 'Criado Em',
            'updated_at' => 'Atualizado Em',
            'last_login_at' => 'Último Login',
        ];
    }

    // ========== IdentityInterface ==========

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null; // Not implemented for web app
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    // ========== Authentication Helpers ==========

    /**
     * Finds user by username.
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by email.
     * @param string $email
     * @return static|null
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Validates password against stored hash.
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model.
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key.
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if ($insert) {
            $this->generateAuthKey();
        }

        return true;
    }

    // ========== Role Helpers ==========

    /**
     * @return bool whether the user is an admin
     */
    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * @return string human-readable status label
     */
    public function getStatusLabel()
    {
        $statuses = [
            self::STATUS_INACTIVE => 'Inativo',
            self::STATUS_ACTIVE => 'Ativo',
        ];
        return $statuses[$this->status] ?? 'Desconhecido';
    }

    /**
     * @return string human-readable role label
     */
    public function getRoleLabel()
    {
        $roles = [
            self::ROLE_USER => 'Cliente',
            self::ROLE_ADMIN => 'Admin',
        ];
        return $roles[$this->role] ?? 'Desconhecido';
    }

    // ========== Relations ==========

    /**
     * Gets query for [[Campaigns]].
     * @return \yii\db\ActiveQuery
     */
    public function getCampaigns()
    {
        return $this->hasMany(Campaign::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[ShortUrls]].
     * @return \yii\db\ActiveQuery
     */
    public function getShortUrls()
    {
        return $this->hasMany(ShortUrl::class, ['user_id' => 'id']);
    }
}
