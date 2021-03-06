<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "pricing_attribute_parent".
 *
 * @property int $id
 * @property int $service_id
 *
 * @property PricingAttributeMatrix[] $pricingAttributeMatrices
 * @property Service $service
 * @property ProvidedServiceCompositePricing[] $providedServiceCompositePricing
 */
class PricingAttributeParent extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pricing_attribute_parent';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['service_id'], 'integer'],
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPricingAttributeMatrices()
    {
        return $this->hasMany(PricingAttributeMatrix::className(), ['pricing_attribute_parent_id' => 'id']);
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
    public function getProvidedServiceCompositePricing()
    {
        return $this->hasOne(ProvidedServiceCompositePricing::className(), ['pricing_attribute_parent_id' => 'id']);
    }

    public function getOptionIdsArray()
    {
        return collect($this->getPricingAttributeMatrices()->asArray()->all())->pluck('service_attribute_option_id')->toArray();
    }

    public function getOptionIdsFormattedName($glue = '_')
    {
        $array = $this->getOptionIdsArray();

        return implode($glue, $array);
    }
}
