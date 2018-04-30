<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "provided_service_area".
 *
 * @property int $id
 * @property int $provided_service_type_id
 * @property string $name
 * @property int $city_id
 *
 * @property City $city
 * @property ProvidedServiceType $providedServiceType
 * @property ProvidedServiceBasePricing[] $providedServiceBasePricings
 * @property ProvidedServiceCoverage[] $providedServiceCoverages
 * @property ProvidedServiceMatrixPricing[] $providedServiceMatrixPricings
 */
class ProvidedServiceArea extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'provided_service_area';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['provided_service_type_id', 'city_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => City::className(), 'targetAttribute' => ['city_id' => 'id']],
            [['provided_service_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProvidedServiceType::className(), 'targetAttribute' => ['provided_service_type_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'provided_service_type_id' => 'Provided Service Type ID',
            'name' => 'Name',
            'city_id' => 'City ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(City::className(), ['id' => 'city_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProvidedServiceType()
    {
        return $this->hasOne(ProvidedServiceType::className(), ['id' => 'provided_service_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProvidedServiceBasePricings()
    {
        return $this->hasMany(ProvidedServiceBasePricing::className(), ['provided_service_area_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProvidedServiceCoverages()
    {
        return $this->hasMany(ProvidedServiceCoverage::className(), ['provided_service_area_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProvidedServiceMatrixPricings()
    {
        return $this->hasMany(ProvidedServiceMatrixPricing::className(), ['provided_service_area_id' => 'id']);
    }
}
