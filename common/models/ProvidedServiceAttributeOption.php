<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "provided_service_attribute_option".
 *
 * @property int $id
 * @property int $provided_service_attribute_id
 * @property int $service_attribute_option_id
 *
 * @property ProvidedServiceAttribute $providedServiceAttribute
 * @property ServiceAttributeOption $serviceAttributeOption
 */
class ProvidedServiceAttributeOption extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'provided_service_attribute_option';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['provided_service_attribute_id', 'service_attribute_option_id'], 'integer'],
            [['provided_service_attribute_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProvidedServiceAttribute::className(), 'targetAttribute' => ['provided_service_attribute_id' => 'id']],
            [['service_attribute_option_id'], 'exist', 'skipOnError' => true, 'targetClass' => ServiceAttributeOption::className(), 'targetAttribute' => ['service_attribute_option_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'provided_service_attribute_id' => 'Provided Service Attribute ID',
            'service_attribute_option_id' => 'Service Attribute Option ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProvidedServiceAttribute()
    {
        return $this->hasOne(ProvidedServiceAttribute::className(), ['id' => 'provided_service_attribute_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceAttributeOption()
    {
        return $this->hasOne(ServiceAttributeOption::className(), ['id' => 'service_attribute_option_id']);
    }
}
