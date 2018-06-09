<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "pricing_attribute".
 *
 * @property int $id
 * @property int $service_attribute_id
 * @property int $price_type_id
 * @property int $pricing_attribute_group_id
 * @property int $service_id
 *
 * @property PriceType $priceType
 * @property ServiceAttribute $serviceAttribute
 * @property ProvidedServiceIndependentPricing[] $providedServiceIndependentPricings
 * @property ProvidedServiceNoImpactPricing[] $providedServiceNoImpactPricings
 * @property PricingAttributeGroup $pricingAttributeGroup
 */
class PricingAttribute extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pricing_attribute';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['service_attribute_id', 'price_type_id', 'service_id'], 'integer'],
            [['price_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => PriceType::className(), 'targetAttribute' => ['price_type_id' => 'id']],
            [['service_attribute_id'], 'exist', 'skipOnError' => true, 'targetClass' => ServiceAttribute::className(), 'targetAttribute' => ['service_attribute_id' => 'id']],
            [['pricing_attribute_group_id'], 'integer'],
            [['pricing_attribute_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => PricingAttributeGroup::className(), 'targetAttribute' => ['pricing_attribute_group_id' => 'id']],
            [['service_id'], 'exist', 'skipOnError' => true, 'targetClass' => Service::className(), 'targetAttribute' => ['service_id' => 'id']]
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
            'price_type_id' => 'Price Type ID',
            'service_id' => 'Service ID'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPriceType()
    {
        return $this->hasOne(PriceType::className(), ['id' => 'price_type_id']);
    }

    public function getPricingAttributeGroup()
    {
        return $this->hasOne(PricingAttributeGroup::className(), ['id' => 'pricing_attribute_group_id']);
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
    public function getProvidedServiceIndependentPricings()
    {
        return $this->hasMany(ProvidedServiceIndependentPricing::className(), ['pricing_attribute_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProvidedServiceNoImpactPricings()
    {
        return $this->hasMany(ProvidedServiceNoImpactPricing::className(), ['pricing_attribute_id' => 'id']);
    }

    public function getService()
    {
        return $this->hasOne(Service::className(), ['id' => 'service_id']);
    }
}
