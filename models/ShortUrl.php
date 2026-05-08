<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "short_url".
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $campaign_id
 * @property string|null $title
 * @property string $original_url
 * @property string $short_code
 * @property string|null $qr_code_path
 * @property int|null $status
 * @property int|null $expires_at
 * @property int|null $password_protected
 * @property string|null $password_hash
 * @property string|null $notes
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Campaign $campaign
 * @property ScanLog[] $scanLogs
 * @property User $user
 */
class ShortUrl extends ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    /** Virtual property — plain-text password from form (gets hashed before save) */
    public $link_password;

    /**
     * Dangerous URL schemes that must be blocked.
     */
    const BLOCKED_SCHEMES = ['javascript:', 'data:', 'file:', 'vbscript:', 'ftp:'];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'short_url';
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
            [['campaign_id', 'title', 'qr_code_path', 'expires_at', 'password_hash', 'notes'], 'default', 'value' => null],
            [['status'], 'default', 'value' => self::STATUS_ACTIVE],
            [['password_protected'], 'default', 'value' => 0],
            [['original_url'], 'required'],
            [['user_id', 'campaign_id', 'status', 'password_protected', 'created_at', 'updated_at'], 'integer'],
            [['expires_at'], 'safe'],
            [['original_url', 'notes'], 'string'],
            [['original_url'], 'url', 'defaultScheme' => 'https'],
            [['original_url'], 'validateSafeUrl'],
            [['title'], 'string', 'max' => 190],
            [['short_code'], 'string', 'max' => 50],
            [['qr_code_path', 'password_hash', 'tags'], 'string', 'max' => 255],
            [['short_code'], 'unique'],
            // Virtual field: plain-text password (never stored directly)
            [['link_password'], 'string', 'min' => 4, 'max' => 100],
            [['link_password'], 'safe'],
            [['campaign_id'], 'exist', 'skipOnError' => true, 'targetClass' => Campaign::class, 'targetAttribute' => ['campaign_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * Validates that the URL doesn't use a dangerous scheme.
     *
     * @param string $attribute
     * @param array $params
     */
    public function validateSafeUrl($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $url = strtolower(trim($this->$attribute));
            foreach (self::BLOCKED_SCHEMES as $scheme) {
                if (strpos($url, $scheme) === 0) {
                    $this->addError($attribute, 'Este tipo de URL não é permitido por razões de segurança.');
                    return;
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Utilizador',
            'campaign_id' => 'Campanha',
            'title' => 'Título',
            'original_url' => 'URL Original',
            'short_code' => 'Código Curto',
            'qr_code_path' => 'QR Code',
            'status' => 'Estado',
            'expires_at' => 'Expira Em',
            'password_protected' => 'Protegido por Password',
            'password_hash' => 'Password',
            'notes' => 'Notas',
            'tags' => 'Tags (separadas por vírgula)',
            'created_at' => 'Criado Em',
            'updated_at' => 'Atualizado Em',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function beforeValidate()
    {
        if (!parent::beforeValidate()) {
            return false;
        }

        // Convert date string to timestamp if it's not already an integer
        if (!empty($this->expires_at) && !is_numeric($this->expires_at)) {
            $timestamp = strtotime($this->expires_at);
            if ($timestamp === false) {
                $this->addError('expires_at', 'Formato de data inválido.');
                return false;
            }
            $this->expires_at = $timestamp;
        }

        return true;
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
            // Auto-assign current user if not set
            if (empty($this->user_id) && !Yii::$app->user->isGuest) {
                $this->user_id = Yii::$app->user->id;
            }

            // Auto-generate short code if not provided
            if (empty($this->short_code)) {
                $this->short_code = $this->generateShortCode();
            }
        }

        // Handle password protection
        if (!empty($this->link_password)) {
            // Hash and store the password
            $this->password_hash = Yii::$app->security->generatePasswordHash($this->link_password);
            $this->password_protected = 1;
        } elseif (!$this->password_protected) {
            // Clear password if protection is disabled
            $this->password_hash = null;
        }

        return true;
    }

    /**
     * Validates a visitor's entered password against the stored hash.
     *
     * @param string $password
     * @return bool
     */
    public function validateLinkPassword($password)
    {
        return $this->password_protected
            && !empty($this->password_hash)
            && Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    // ========== Short Code Generation ==========

    /**
     * Generates a unique short code.
     *
     * @param int $length
     * @return string
     */
    public function generateShortCode($length = 6)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $maxAttempts = 10;

        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            $code = '';
            for ($i = 0; $i < $length; $i++) {
                $code .= $characters[random_int(0, strlen($characters) - 1)];
            }

            // Check uniqueness
            if (!static::find()->where(['short_code' => $code])->exists()) {
                return $code;
            }
        }

        // Fallback: increase length
        return $this->generateShortCode($length + 1);
    }

    // ========== Status Helpers ==========

    /**
     * @return bool whether the link is expired
     */
    public function isExpired()
    {
        return $this->expires_at !== null && $this->expires_at < time();
    }

    /**
     * @return bool whether the link is active and not expired
     */
    public function isActive()
    {
        return $this->status == self::STATUS_ACTIVE && !$this->isExpired();
    }

    /**
     * @return string human-readable status label
     */
    public function getStatusLabel()
    {
        if ($this->isExpired()) {
            return 'Expirado';
        }
        $statuses = [
            self::STATUS_INACTIVE => 'Inativo',
            self::STATUS_ACTIVE => 'Ativo',
        ];
        return $statuses[$this->status] ?? 'Desconhecido';
    }

    /**
     * @return string CSS class for status badge
     */
    public function getStatusBadgeClass()
    {
        if ($this->isExpired()) {
            return 'badge-warning';
        }
        return $this->status == self::STATUS_ACTIVE ? 'badge-success' : 'badge-danger';
    }

    // ========== URL Helpers ==========

    /**
     * Returns the full short URL.
     *
     * @return string
     */
    public function getShortUrl()
    {
        $baseUrl = Yii::$app->params['baseUrl'] ?? Yii::$app->request->hostInfo;
        return rtrim($baseUrl, '/') . '/' . $this->short_code;
    }

    // ========== Statistics ==========

    /**
     * @return int total number of scans for this link
     */
    public function getTotalScans()
    {
        return (int) $this->getScanLogs()->count();
    }

    /**
     * @return int number of unique scans (by IP) for this link
     */
    public function getUniqueScans()
    {
        return (int) $this->getScanLogs()
            ->select('ip_address')
            ->distinct()
            ->count();
    }

    /**
     * Returns daily scan counts for the last N days.
     *
     * @param int $days
     * @return array ['date' => count, ...]
     */
    public function getDailyScans($days = 30)
    {
        $startTime = strtotime("-{$days} days");

        $rows = ScanLog::find()
            ->select([
                'scan_date' => 'DATE(FROM_UNIXTIME(accessed_at))',
                'scan_count' => 'COUNT(*)',
            ])
            ->where(['short_url_id' => $this->id])
            ->andWhere(['>=', 'accessed_at', $startTime])
            ->groupBy('scan_date')
            ->orderBy('scan_date')
            ->asArray()
            ->all();

        $result = [];
        foreach ($rows as $row) {
            $result[$row['scan_date']] = (int) $row['scan_count'];
        }

        return $result;
    }

    // ========== Relations ==========

    /**
     * Gets query for [[Campaign]].
     * @return \yii\db\ActiveQuery
     */
    public function getCampaign()
    {
        return $this->hasOne(Campaign::class, ['id' => 'campaign_id']);
    }

    /**
     * Gets query for [[ScanLogs]].
     * @return \yii\db\ActiveQuery
     */
    public function getScanLogs()
    {
        return $this->hasMany(ScanLog::class, ['short_url_id' => 'id']);
    }

    /**
     * Gets query for [[User]].
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
