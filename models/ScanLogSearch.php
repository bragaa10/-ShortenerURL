<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ScanLog;

/**
 * ScanLogSearch represents the model behind the search form of `app\models\ScanLog`.
 */
class ScanLogSearch extends ScanLog
{
    public $short_url_title;
    public $q; // Global search query

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'short_url_id', 'accessed_at', 'created_at'], 'integer'],
            [['ip_address', 'user_agent', 'referer', 'source', 'country', 'city', 'device_type', 'os', 'browser', 'language', 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'short_url_title', 'q'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @param string|null $formName Form name to be used into `->load()` method.
     *
     * @return ActiveDataProvider
     */
    public function search($params, $formName = null)
    {
        $query = ScanLog::find()->joinWith(['shortUrl' => function($q) {
            $q->alias('short_url');
        }]);

        // add conditions that should always apply here
        if (!Yii::$app->user->identity->isAdmin()) {
            $query->andWhere(['short_url.user_id' => Yii::$app->user->id]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
                'attributes' => [
                    'id' => [
                        'asc' => ['scan_log.id' => SORT_ASC],
                        'desc' => ['scan_log.id' => SORT_DESC],
                        'label' => 'ID',
                    ],
                    'accessed_at',
                    'ip_address',
                    'country',
                    'city',
                    'browser',
                    'os',
                    'device_type',
                    'source',
                    'short_url_title' => [
                        'asc' => ['short_url.title' => SORT_ASC],
                        'desc' => ['short_url.title' => SORT_DESC],
                        'label' => 'Link',
                    ],
                ],
            ],
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // Global search filtering conditions
        if ($this->q) {
            $query->andFilterWhere(['or',
                ['like', 'scan_log.ip_address', $this->q],
                ['like', 'scan_log.country', $this->q],
                ['like', 'scan_log.city', $this->q],
                ['like', 'scan_log.browser', $this->q],
                ['like', 'short_url.title', $this->q],
                ['like', 'short_url.short_code', $this->q],
            ]);
        }

        // grid filtering conditions (individual columns)
        $query->andFilterWhere([
            'scan_log.id' => $this->id,
            'scan_log.short_url_id' => $this->short_url_id,
            'scan_log.accessed_at' => $this->accessed_at,
            'scan_log.created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'scan_log.ip_address', $this->ip_address])
            ->andFilterWhere(['like', 'scan_log.user_agent', $this->user_agent])
            ->andFilterWhere(['like', 'scan_log.referer', $this->referer])
            ->andFilterWhere(['like', 'scan_log.source', $this->source])
            ->andFilterWhere(['like', 'scan_log.country', $this->country])
            ->andFilterWhere(['like', 'scan_log.city', $this->city])
            ->andFilterWhere(['like', 'scan_log.device_type', $this->device_type])
            ->andFilterWhere(['like', 'scan_log.os', $this->os])
            ->andFilterWhere(['like', 'scan_log.browser', $this->browser])
            ->andFilterWhere(['like', 'short_url.title', $this->short_url_title]);

        return $dataProvider;
    }
}
