<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ProvidedServiceCoverage;

/**
 * ProvidedServiceCoverageSearch represents the model behind the search form of `common\models\ProvidedServiceCoverage`.
 */
class ProvidedServiceCoverageSearch extends ProvidedServiceCoverage
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'provided_service_area_id'], 'integer'],
            [['lat', 'lng'], 'safe'],
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
        $query = ProvidedServiceCoverage::find();

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
            'provided_service_area_id' => $this->provided_service_area_id,
        ]);

        $query->andFilterWhere(['like', 'lat', $this->lat])
            ->andFilterWhere(['like', 'lng', $this->lng]);

        return $dataProvider;
    }
}
