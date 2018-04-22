<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "price_type".
 *
 * @property int $id
 * @property string $type
 *
 * @property PricingAttribute[] $pricingAttributes
 */
class PriceType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'price_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPricingAttributes()
    {
        return $this->hasMany(PricingAttribute::className(), ['price_type_id' => 'id']);
    }
}
