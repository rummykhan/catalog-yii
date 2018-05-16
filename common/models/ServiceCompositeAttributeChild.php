<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "service_composite_attribute_child".
 *
 * @property int $id
 * @property int $service_attribute_id
 * @property int $service_attribute_option_id
 * @property int $service_composite_attribute_parent_id
 *
 * @property ServiceCompositeAttributeParent $serviceCompositeAttributeParent
 * @property ServiceAttribute $serviceAttribute
 * @property ServiceAttributeOption $serviceAttributeOption
 */
class ServiceCompositeAttributeChild extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'service_composite_attribute_child';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['service_attribute_id', 'service_attribute_option_id', 'service_composite_attribute_parent_id'], 'integer'],
            [['service_composite_attribute_parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => ServiceCompositeAttributeParent::className(), 'targetAttribute' => ['service_composite_attribute_parent_id' => 'id']],
            [['service_attribute_id'], 'exist', 'skipOnError' => true, 'targetClass' => ServiceAttribute::className(), 'targetAttribute' => ['service_attribute_id' => 'id']],
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
            'service_attribute_id' => 'Service Attribute ID',
            'service_attribute_option_id' => 'Service Attribute Option ID',
            'service_composite_attribute_parent_id' => 'Service Composite Attribute Parent ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceCompositeAttributeParent()
    {
        return $this->hasOne(ServiceCompositeAttributeParent::className(), ['id' => 'service_composite_attribute_parent_id']);
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
    public function getServiceAttributeOption()
    {
        return $this->hasOne(ServiceAttributeOption::className(), ['id' => 'service_attribute_option_id']);
    }
}
