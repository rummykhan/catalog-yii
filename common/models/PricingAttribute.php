<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "pricing_attribute".
 *
 * @property int $id
 * @property int $service_attribute_id
 * @property int $price_type_id
 *
 * @property PriceType $priceType
 * @property ServiceAttribute $serviceAttribute
 * @property ProvidedServiceBasePricing[] $providedServiceBasePricings
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
            [['service_attribute_id', 'price_type_id'], 'integer'],
            [['price_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => PriceType::className(), 'targetAttribute' => ['price_type_id' => 'id']],
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
            'service_attribute_id' => 'Service Attribute ID',
            'price_type_id' => 'Price Type ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPriceType()
    {
        return $this->hasOne(PriceType::className(), ['id' => 'price_type_id']);
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
    public function getProvidedServiceBasePricings()
    {
        return $this->hasMany(ProvidedServiceBasePricing::className(), ['pricing_attribute_id' => 'id']);
    }
}
