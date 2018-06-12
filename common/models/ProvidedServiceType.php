<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "provided_service_type".
 *
 * @property int $id
 * @property int $provided_service_id
 * @property int $service_request_type_id
 * @property int $calendar_id
 * @property int $service_area_id
 * @property boolean $deleted
 *
 * @property ServiceRequestType $serviceRequestType
 * @property ProvidedService $providedService
 * @property Calendar $calendar
 * @property ServiceArea $serviceArea
 */
class ProvidedServiceType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'provided_service_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['provided_service_id', 'calendar_id', 'service_area_id', 'service_request_type_id'], 'integer'],
            [['service_request_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ServiceRequestType::className(), 'targetAttribute' => ['service_request_type_id' => 'id']],
            [['provided_service_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProvidedService::className(), 'targetAttribute' => ['provided_service_id' => 'id']],
            [['calendar_id'], 'exist', 'skipOnError' => true, 'targetClass' => Calendar::className(), 'targetAttribute' => ['calendar_id' => 'id']],
            [['service_area_id'], 'exist', 'skipOnError' => true, 'targetClass' => ServiceArea::className(), 'targetAttribute' => ['service_area_id' => 'id']],
            ['deleted', 'boolean']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'provided_service_id' => 'Provided Service ID',
            'service_request_type_id' => 'Service Type ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProvidedService()
    {
        return $this->hasOne(ProvidedService::className(), ['id' => 'provided_service_id']);
    }

    public function getServiceRequestType()
    {
        return $this->hasOne(ServiceRequestType::className(), ['id' => 'service_request_type_id']);
    }

    public function getCalendar()
    {
        return $this->hasOne(Calendar::className(), ['id' => 'calendar_id']);
    }

    public function getServiceArea()
    {
        return $this->hasOne(ServiceArea::className(), ['id' => 'service_area_id']);
    }

    public function getRequestTypeLabel()
    {
        return $this->serviceRequestType->requestType->name;
    }

    public function validateUnique()
    {
        $count = ProvidedServiceType::find()
            ->where(['provided_service_id' => $this->provided_service_id])
            ->andWhere(['calendar_id' => $this->calendar_id])
            ->andWhere(['service_area_id' => $this->service_area_id])
            ->andWhere(['service_request_type_id' => $this->service_request_type_id])
            ->count();

        return $count == 0;
    }
}
