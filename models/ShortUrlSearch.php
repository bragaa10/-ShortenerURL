<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ShortUrl;

/**
 * ShortUrlSearch represents the model behind the search form of `app\models\ShortUrl`.
 */
class ShortUrlSearch extends ShortUrl
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'campaign_id', 'status', 'expires_at', 'password_protected', 'created_at', 'updated_at'], 'integer'],
            [['title', 'original_url', 'short_code', 'qr_code_path', 'password_hash', 'notes'], 'safe'],
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
        $query = ShortUrl::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'campaign_id' => $this->campaign_id,
            'status' => $this->status,
            'expires_at' => $this->expires_at,
            'password_protected' => $this->password_protected,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'original_url', $this->original_url])
            ->andFilterWhere(['like', 'short_code', $this->short_code])
            ->andFilterWhere(['like', 'qr_code_path', $this->qr_code_path])
            ->andFilterWhere(['like', 'password_hash', $this->password_hash])
            ->andFilterWhere(['like', 'notes', $this->notes]);

        return $dataProvider;
    }
}
