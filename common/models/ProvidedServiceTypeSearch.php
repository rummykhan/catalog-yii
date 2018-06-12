<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\db\Query;

class ProvidedServiceTypeSearch extends ProvidedServiceType
{
    public $request_type;
    public $calendar_name;
    public $service_area;
    public $provided_service_id;

    public function rules()
    {
        return [
            [['request_type', 'calendar_name', 'service_area'], 'safe']
        ];
    }

    public function search($params)
    {
        $this->load($params);

        $query = (new Query())
            ->select([
                'provided_service_type.id',
                new Expression('service.name as service_name'),
                new Expression('request_type.name as request_type'),
                new Expression('calendar.name as calendar_name'),
                new Expression('service_area.name as service_area')
            ])
            ->from('provided_service_type')
            ->join('inner join', 'provided_service', 'provided_service_type.provided_service_id=provided_service.id')
            ->join('inner join', 'calendar', 'provided_service_type.calendar_id=calendar.id')
            ->join('inner join', 'service_area', 'provided_service_type.service_area_id=service_area.id')
            ->join('inner join', 'service_request_type', 'provided_service_type.service_request_type_id=service_request_type.id')
            ->join('inner join', 'request_type', 'service_request_type.request_type_id=request_type.id')
            ->join('inner join', 'service', 'provided_service.service_id=service.id')
            ->where(['provided_service.id' => $this->provided_service_id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['LIKE', 'request_type.name', $this->request_type])
            ->andFilterWhere(['LIKE', 'calendar.name', $this->calendar_name])
            ->andFilterWhere(['LIKE', 'service_area.name', $this->service_area]);

        return $dataProvider;
    }
}
