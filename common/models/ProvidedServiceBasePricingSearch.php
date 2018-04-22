<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ProvidedServiceBasePricing;

/**
 * ProvidedServiceBasePricingSearch represents the model behind the search form of `common\models\ProvidedServiceBasePricing`.
 */
class ProvidedServiceBasePricingSearch extends ProvidedServiceBasePricing
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'provided_service_id', 'pricing_attribute_id'], 'integer'],
            [['base_price'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
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
        $query = ProvidedServiceBasePricing::find();

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
            'provided_service_id' => $this->provided_service_id,
            'pricing_attribute_id' => $this->pricing_attribute_id,
            'base_price' => $this->base_price,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        return $dataProvider;
    }
}
