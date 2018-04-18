<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "service_attribute".
 *
 * @property int $id
 * @property int $service_id
 * @property int $attribute_id
 *
 * @property ProvidedServiceAttribute[] $providedServiceAttributes
 * @property Attribute $attribute0
 * @property Service $service
 * @property ServiceAttributeOption[] $serviceAttributeOptions
 */
class ServiceAttribute extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'service_attribute';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['service_id', 'attribute_id'], 'integer'],
            [['attribute_id'], 'exist', 'skipOnError' => true, 'targetClass' => Attribute::className(), 'targetAttribute' => ['attribute_id' => 'id']],
            [['service_id'], 'exist', 'skipOnError' => true, 'targetClass' => Service::className(), 'targetAttribute' => ['service_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'service_id' => 'Service ID',
            'attribute_id' => 'Attribute ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProvidedServiceAttributes()
    {
        return $this->hasMany(ProvidedServiceAttribute::className(), ['service_attribute_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAttribute0()
    {
        return $this->hasOne(Attribute::className(), ['id' => 'attribute_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getService()
    {
        return $this->hasOne(Service::className(), ['id' => 'service_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceAttributeOptions()
    {
        return $this->hasMany(ServiceAttributeOption::className(), ['service_attribute_id' => 'id']);
    }
}
