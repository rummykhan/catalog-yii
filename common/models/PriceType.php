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
    const TYPE_COMPOSITE = 'composite';
    const TYPE_INCREMENTAL = 'incremental';
    const TYPE_NO_IMPACT = 'no impact';

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

    /**
     * @param $priceType PriceType
     * @return mixed
     */
    public static function getName($priceType)
    {
        return base64_encode($priceType->type . $priceType->id);
    }

    /**
     *
     */
    public static function toList()
    {
        return collect(static::find()->all())->pluck('type', 'id');
    }
}
