<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "scan_log".
 *
 * @property int $id
 * @property int $short_url_id
 * @property int $accessed_at
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string|null $referer
 * @property string|null $source
 * @property string|null $country
 * @property string|null $city
 * @property string|null $device_type
 * @property string|null $os
 * @property string|null $browser
 * @property string|null $language
 * @property string|null $utm_source
 * @property string|null $utm_medium
 * @property string|null $utm_campaign
 * @property string|null $utm_term
 * @property string|null $utm_content
 * @property int $created_at
 *
 * @property ShortUrl $shortUrl
 */
class ScanLog extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'scan_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ip_address', 'user_agent', 'referer', 'source', 'country', 'city', 'device_type', 'os', 'browser', 'language', 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'], 'default', 'value' => null],
            [['short_url_id', 'accessed_at', 'created_at'], 'required'],
            [['short_url_id', 'accessed_at', 'created_at'], 'integer'],
            [['user_agent', 'referer'], 'string'],
            [['ip_address'], 'string', 'max' => 45],
            [['source', 'device_type'], 'string', 'max' => 50],
            [['country', 'city', 'os', 'browser', 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'], 'string', 'max' => 100],
            [['language'], 'string', 'max' => 20],
            [['short_url_id'], 'exist', 'skipOnError' => true, 'targetClass' => ShortUrl::class, 'targetAttribute' => ['short_url_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'short_url_id' => 'Short Url ID',
            'accessed_at' => 'Accessed At',
            'ip_address' => 'Ip Address',
            'user_agent' => 'User Agent',
            'referer' => 'Referer',
            'source' => 'Source',
            'country' => 'Country',
            'city' => 'City',
            'device_type' => 'Device Type',
            'os' => 'Os',
            'browser' => 'Browser',
            'language' => 'Language',
            'utm_source' => 'Utm Source',
            'utm_medium' => 'Utm Medium',
            'utm_campaign' => 'Utm Campaign',
            'utm_term' => 'Utm Term',
            'utm_content' => 'Utm Content',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[ShortUrl]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getShortUrl()
    {
        return $this->hasOne(ShortUrl::class, ['id' => 'short_url_id']);
    }

}
