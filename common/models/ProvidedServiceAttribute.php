<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "provided_service_attribute".
 *
 * @property int $id
 * @property int $provided_service_id
 * @property int $service_attribute_id
 *
 * @property ProvidedService $providedService
 * @property ServiceAttribute $serviceAttribute
 * @property ProvidedServiceAttributeOption[] $providedServiceAttributeOptions
 */
class ProvidedServiceAttribute extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'provided_service_attribute';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['provided_service_id', 'service_attribute_id'], 'integer'],
            [['provided_service_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProvidedService::className(), 'targetAttribute' => ['provided_service_id' => 'id']],
            [['service_attribute_id'], 'exist', 'skipOnError' => true, 'targetClass' => ServiceAttribute::className(), 'targetAttribute' => ['service_attribute_id' => 'id']],
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
            'service_attribute_id' => 'Service Attribute ID',
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()')
            ]
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProvidedService()
    {
        return $this->hasOne(ProvidedService::className(), ['id' => 'provided_service_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceAttribute()
    {
        return $this->hasOne(ServiceAttribute::className(), ['id' => 'service_attribute_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProvidedServiceAttributeOptions()
    {
        return $this->hasMany(ProvidedServiceAttributeOption::className(), ['provided_service_attribute_id' => 'id']);
    }
}
