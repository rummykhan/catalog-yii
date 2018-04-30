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
            [['provided_service_type_id'], 'integer'],
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
                'provided_service_area.id',
                new Expression('city.name as city'),
                'service_type.type',
                new Expression('service_type.id service_type_id'),
                'provided_service_area.name',
                new Expression('provided_service_type.provided_service_id'),
            ])
            ->from('provided_service_area')
            ->join('inner join', 'provided_service_type', 'provided_service_area.provided_service_type_id=provided_service_type.id')
            ->join('inner join', 'city', 'provided_service_area.city_id=city.id')
            ->join('inner join', 'service_type', 'provided_service_type.service_type_id=service_type.id')
            ->andWhere(['provided_service_type.provided_service_id' => $this->provided_service_id]);

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
