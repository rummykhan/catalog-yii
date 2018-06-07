<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "provided_service_type".
 *
 * @property int $id
 * @property int $provided_service_id
 * @property int $service_request_type_id
 * @property boolean $deleted
 *
 * @property ProvidedServiceArea[] $providedServiceAreas
 * @property ServiceRequestType $serviceRequestType
 * @property ProvidedService $providedService
 */
class ProvidedRequestType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'provided_request_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['provided_service_id', 'service_request_type_id'], 'integer'],
            ['deleted', 'boolean'],
            [['service_request_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => RequestType::className(), 'targetAttribute' => ['service_request_type_id' => 'id']],
            [['provided_service_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProvidedService::className(), 'targetAttribute' => ['provided_service_id' => 'id']],
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
    public function getProvidedServiceAreas()
    {
        return $this->hasMany(ProvidedServiceArea::className(), ['provided_request_type_id' => 'id']);
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

    public function getRequestTypeLabel()
    {
        return $this->serviceRequestType->requestType->name;
    }
}
