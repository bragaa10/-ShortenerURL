<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ShortUrl;

/**
 * ShortUrlSearch represents the model behind the search form of `app\models\ShortUrl`.
 */
class ShortUrlSearch extends ShortUrl
{
    public $campaign_name;
    public $q; // Global search query

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'campaign_id', 'status', 'expires_at', 'password_protected', 'created_at', 'updated_at'], 'integer'],
            [['title', 'original_url', 'short_code', 'qr_code_path', 'password_hash', 'notes', 'tags', 'campaign_name', 'q'], 'safe'],
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
        $query = ShortUrl::find()->joinWith(['campaign']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        // Enable sorting by campaign name
        $dataProvider->sort->attributes['campaign_name'] = [
            'asc' => ['campaign.name' => SORT_ASC],
            'desc' => ['campaign.name' => SORT_DESC],
        ];

        $this->load($params, $formName);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // Global search filtering conditions
        if ($this->q) {
            $query->andFilterWhere(['or',
                ['like', 'short_url.title', $this->q],
                ['like', 'campaign.name', $this->q],
                ['like', 'short_url.short_code', $this->q],
            ]);
        }

        // grid filtering conditions (individual columns)
        $query->andFilterWhere([
            'short_url.id' => $this->id,
            'short_url.user_id' => $this->user_id,
            'short_url.campaign_id' => $this->campaign_id,
            'short_url.status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'short_url.title', $this->title])
            ->andFilterWhere(['like', 'short_url.original_url', $this->original_url])
            ->andFilterWhere(['like', 'short_url.short_code', $this->short_code])
            ->andFilterWhere(['like', 'campaign.name', $this->campaign_name]);

        return $dataProvider;
    }
}
