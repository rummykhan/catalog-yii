<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "service_request_type".
 *
 * @property int $id
 * @property int $service_id
 * @property int $request_type_id
 * @property boolean $deleted
 *
 * @property ProvidedRequestType[] $providedRequestTypes
 * @property RequestType $requestType
 * @property Service $service
 */
class ServiceRequestType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'service_request_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['service_id', 'request_type_id'], 'integer'],
            [['request_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => RequestType::className(), 'targetAttribute' => ['request_type_id' => 'id']],
            [['service_id'], 'exist', 'skipOnError' => true, 'targetClass' => Service::className(), 'targetAttribute' => ['service_id' => 'id']],
            ['deleted', 'boolean']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'service_id' => 'Service ID',
            'request_type_id' => 'Request Type ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProvidedRequestTypes()
    {
        return $this->hasMany(ProvidedRequestType::className(), ['service_request_type_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRequestType()
    {
        return $this->hasOne(RequestType::className(), ['id' => 'request_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getService()
    {
        return $this->hasOne(Service::className(), ['id' => 'service_id']);
    }
}
