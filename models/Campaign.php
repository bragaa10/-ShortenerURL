<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "campaign".
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string|null $description
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 *
 * @property ShortUrl[] $shortUrls
 * @property User $user
 */
class Campaign extends ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'campaign';
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
            [['description'], 'default', 'value' => null],
            [['status'], 'default', 'value' => self::STATUS_ACTIVE],
            [['name'], 'required'],
            [['user_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['description'], 'string'],
            [['name'], 'string', 'max' => 190],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Utilizador',
            'name' => 'Nome',
            'description' => 'Descrição',
            'status' => 'Estado',
            'created_at' => 'Criado Em',
            'updated_at' => 'Atualizado Em',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        if ($insert && empty($this->user_id) && !Yii::$app->user->isGuest) {
            $this->user_id = Yii::$app->user->id;
        }
        return true;
    }

    /**
     * @return string human-readable status label
     */
    public function getStatusLabel()
    {
        return $this->status == self::STATUS_ACTIVE ? 'Ativa' : 'Inativa';
    }

    /**
     * @return int total scans across all links in this campaign
     */
    public function getTotalScans()
    {
        return (int) ScanLog::find()
            ->innerJoin('short_url', 'short_url.id = scan_log.short_url_id')
            ->where(['short_url.campaign_id' => $this->id])
            ->count();
    }

    /**
     * Gets query for [[ShortUrls]].
     * @return \yii\db\ActiveQuery
     */
    public function getShortUrls()
    {
        return $this->hasMany(ShortUrl::class, ['campaign_id' => 'id']);
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
