<?php

namespace common\models;

use common\helpers\MatrixHelper;
use Yii;

/**
 * This is the model class for table "pricing_attribute_matrix".
 *
 * @property int $id
 * @property int $pricing_attribute_parent_id
 * @property int $service_attribute_option_id
 *
 * @property PricingAttributeParent $pricingAttributeParent
 * @property ServiceAttributeOption $serviceAttributeOption
 */
class PricingAttributeMatrix extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pricing_attribute_matrix';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pricing_attribute_parent_id', 'service_attribute_option_id'], 'integer'],
            [['pricing_attribute_parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => PricingAttributeParent::className(), 'targetAttribute' => ['pricing_attribute_parent_id' => 'id']],
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
            'pricing_attribute_parent_id' => 'Pricing Attribute Parent ID',
            'service_attribute_option_id' => 'Service Attribute Option ID',
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
    public function getServiceAttributeOption()
    {
        return $this->hasOne(ServiceAttributeOption::className(), ['id' => 'service_attribute_option_id']);
    }
}
