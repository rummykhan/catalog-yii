<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "provided_service_no_impact_pricing".
 *
 * @property int $id
 * @property int $pricing_attribute_id
 * @property int $provided_service_type_id
 * @property int $service_attribute_option_id
 * @property string $created_at
 * @property string $updated_at
 *
 * @property ServiceAttributeOption $serviceAttributeOption
 * @property PricingAttribute $pricingAttribute
 * @property ProvidedServiceArea $providedServiceArea
 */
class ProvidedServiceNoImpactPricing extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'provided_service_no_impact_pricing';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pricing_attribute_id', 'provided_service_type_id', 'service_attribute_option_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['service_attribute_option_id'], 'exist', 'skipOnError' => true, 'targetClass' => ServiceAttributeOption::className(), 'targetAttribute' => ['service_attribute_option_id' => 'id']],
            [['pricing_attribute_id'], 'exist', 'skipOnError' => true, 'targetClass' => PricingAttribute::className(), 'targetAttribute' => ['pricing_attribute_id' => 'id']],
            [['provided_service_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProvidedServiceArea::className(), 'targetAttribute' => ['provided_service_type_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pricing_attribute_id' => 'Pricing Attribute ID',
            'provided_service_type_id' => 'Provided Service Area ID',
            'service_attribute_option_id' => 'Service Attribute Option ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'active' => 'Is Active'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceAttributeOption()
    {
        return $this->hasOne(ServiceAttributeOption::className(), ['id' => 'service_attribute_option_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPricingAttribute()
    {
        return $this->hasOne(PricingAttribute::className(), ['id' => 'pricing_attribute_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProvidedServiceArea()
    {
        return $this->hasOne(ProvidedServiceArea::className(), ['id' => 'provided_service_type_id']);
    }
}
