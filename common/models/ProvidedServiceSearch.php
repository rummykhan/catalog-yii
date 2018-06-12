<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ProvidedService;
use yii\db\Expression;
use yii\db\Query;

/**
 * ProvidedServiceSearch represents the model behind the search form of `common\models\ProvidedService`.
 */
class ProvidedServiceSearch extends ProvidedService
{
    public $service_name;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'service_id', 'provider_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            ['service_name', 'safe']
        ];
    }

    /**
     * @inheritdoc
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
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = (new Query())
            ->select([
                'provided_service.id',
                'provided_service.service_id',
                'provided_service.provider_id',
                new Expression('service.name as service_name')
            ])
            ->from('provided_service')
            ->join('inner join', 'service', 'provided_service.service_id=service.id')
            ->where(['provided_service.provider_id' => $this->provider_id]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'service_id' => $this->service_id,
            'provider_id' => $this->provider_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['LIKE', 'service.name', $this->service_name]);

        return $dataProvider;
    }
}
