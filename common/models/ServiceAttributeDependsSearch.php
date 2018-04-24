<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ServiceAttributeDepends;

/**
 * ServiceAttributeDependsSearch represents the model behind the search form of `common\models\ServiceAttributeDepends`.
 */
class ServiceAttributeDependsSearch extends ServiceAttributeDepends
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'service_attribute_id', 'depends_on_id', 'service_attribute_option_id'], 'integer'],
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
        $query = ServiceAttributeDepends::find();

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
            'service_attribute_id' => $this->service_attribute_id,
            'depends_on_id' => $this->depends_on_id,
            'service_attribute_option_id' => $this->service_attribute_option_id,
        ]);

        return $dataProvider;
    }
}
