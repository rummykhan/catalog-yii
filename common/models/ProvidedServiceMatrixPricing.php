<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "provided_service_matrix_pricing".
 *
 * @property int $id
 * @property int $provided_service_id
 * @property int $pricing_attribute_parent_id
 * @property double $price
 * @property string $created_at
 * @property string $updated_at
 *
 * @property PricingAttributeParent $pricingAttributeParent
 * @property ProvidedService $providedService
 */
class ProvidedServiceMatrixPricing extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'provided_service_matrix_pricing';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['provided_service_id', 'pricing_attribute_parent_id'], 'integer'],
            [['price'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['pricing_attribute_parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => PricingAttributeParent::className(), 'targetAttribute' => ['pricing_attribute_parent_id' => 'id']],
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
            'pricing_attribute_parent_id' => 'Pricing Attribute Parent ID',
            'price' => 'Price',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPricingAttributeParent()
    {
        return $this->hasOne(PricingAttributeParent::className(), ['id' => 'pricing_attribute_parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProvidedService()
    {
        return $this->hasOne(ProvidedService::className(), ['id' => 'provided_service_id']);
    }
}
