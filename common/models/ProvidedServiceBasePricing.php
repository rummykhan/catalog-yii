<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "provided_service_base_pricing".
 *
 * @property int $id
 * @property int $provided_service_id
 * @property int $pricing_attribute_id
 * @property double $base_price
 * @property int $provided_service_area_id
 * @property int $service_attribute_option_id
 * @property string $created_at
 * @property string $updated_at
 *
 * @property PricingAttribute $pricingAttribute
 * @property ProvidedService $providedService
 * @property ProvidedServiceArea $providedServiceArea
 */
class ProvidedServiceBasePricing extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'provided_service_base_pricing';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['provided_service_id', 'pricing_attribute_id'], 'integer'],
            [['base_price'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['pricing_attribute_id'], 'exist', 'skipOnError' => true, 'targetClass' => PricingAttribute::className(), 'targetAttribute' => ['pricing_attribute_id' => 'id']],
            [['provided_service_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProvidedService::className(), 'targetAttribute' => ['provided_service_id' => 'id']],
            [['provided_service_area_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProvidedServiceArea::className(), 'targetAttribute' => ['provided_service_area_id' => 'id']],
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
            'provided_service_id' => 'Provided Service ID',
            'pricing_attribute_id' => 'Pricing Attribute ID',
            'base_price' => 'Base Price',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
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
    public function getProvidedService()
    {
        return $this->hasOne(ProvidedService::className(), ['id' => 'provided_service_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProvidedServiceArea()
    {
        return $this->hasOne(ProvidedServiceArea::className(), ['id' => 'provided_service_area_id']);
    }
}
