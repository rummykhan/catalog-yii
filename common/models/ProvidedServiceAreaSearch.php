<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ProvidedServiceArea;
use yii\db\Expression;
use yii\db\Query;

/**
 * ProvidedServiceAreaSearch represents the model behind the search form of `common\models\ProvidedServiceArea`.
 */
class ProvidedServiceAreaSearch extends ProvidedServiceArea
{
    public $provided_service_id;
    public $city;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['provided_request_type_id'], 'integer'],
            [['name'], 'safe'],
            [['city'], 'safe'],
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
                'provided_service_type.id',
                new Expression('service_area.id as service_area_id'),
                new Expression('city.name as city'),
                new Expression('request_type.name as request_type'),
                new Expression('request_type.id as request_type_id'),
                'service_area.name',
                new Expression('provided_request_type.provided_service_id'),
                new Expression('provided_request_type.id as provided_request_type'),
            ])
            ->from('service_area')
            ->join('inner join', 'provided_service_type', 'service_area.id=provided_service_type.id')
            ->join('inner join', 'provided_request_type', 'provided_service_type.provided_request_type_id=provided_request_type.id')
            ->join('inner join', 'city', 'service_area.city_id=city.id')
            ->join('inner join', 'service_request_type', 'provided_request_type.service_request_type_id=service_request_type.id')
            ->join('inner join', 'request_type', 'service_request_type.request_type_id=request_type.id')
            ->andWhere(['provided_request_type.provided_service_id' => $this->provided_service_id]);

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

        return $dataProvider;
    }
}
