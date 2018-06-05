<?php

namespace common\models;

use common\helpers\AvailabilityHelper;
use Yii;

/**
 * This is the model class for table "provided_service_area".
 *
 * @property int $id
 * @property int $provided_request_type_id
 * @property string $name
 * @property int $city_id
 *
 * @property City $city
 * @property ProvidedRequestType $providedServiceType
 *
 * @property ProvidedServiceIndependentPricing[] $providedServiceIndependentPricings
 * @property ProvidedServiceNoImpactPricing[] $providedServiceNoImpactPricings
 * @property ProvidedServiceCompositePricing[] $providedServiceCompositePricings
 *
 * @property ServiceAreaCoverage[] $providedServiceCoverages
 * @property GlobalAvailabilityRule[] $globalAvailabilityRules
 * @property AvailabilityRule[] $availabilityRules
 * @property GlobalAvailabilityException[] $globalAvailabilityExceptions
 * @property AvailabilityException[] $availabilityExceptions
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
            [['provided_request_type_id'], 'integer'],
            [['provided_request_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProvidedRequestType::className(), 'targetAttribute' => ['provided_request_type_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'provided_request_type_id' => 'Provided Service Type ID',
            'name' => 'Name',
            'city_id' => 'City ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProvidedRequestType()
    {
        return $this->hasOne(ProvidedRequestType::className(), ['id' => 'provided_request_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProvidedServiceIndependentPricings()
    {
        return $this->hasMany(ProvidedServiceIndependentPricing::className(), ['provided_service_area_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProvidedServiceNoImpactPricings()
    {
        return $this->hasMany(ProvidedServiceNoImpactPricing::className(), ['provided_service_area_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProvidedServiceCoverages()
    {
        return $this->hasMany(ServiceAreaCoverage::className(), ['provided_service_area_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProvidedServiceCompositePricings()
    {
        return $this->hasMany(ProvidedServiceCompositePricing::className(), ['provided_service_area_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGlobalAvailabilityRules()
    {
        return $this->hasMany(GlobalAvailabilityRule::className(), ['provided_service_area_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAvailabilityRules()
    {
        return $this->hasMany(AvailabilityRule::className(), ['provided_service_area_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGlobalAvailabilityExceptions()
    {
        return $this->hasMany(GlobalAvailabilityException::className(), ['provided_service_area_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAvailabilityExceptions()
    {
        return $this->hasMany(AvailabilityException::className(), ['provided_service_area_id' => 'id']);
    }

    public function getGlobalRules()
    {
        $availabilityRules = collect($this->globalAvailabilityRules)
            ->map(function ($rule) {
                /**@var GlobalAvailabilityRule $rule */
                $identifier = $rule->day . $rule->start_time . $rule->end_time . AvailabilityHelper::AVAILABLE;
                return [
                    'day' => $rule->day,
                    'start_time' => $rule->start_time,
                    'end_time' => $rule->end_time,
                    'type' => AvailabilityHelper::AVAILABLE,
                    'identifier' => $identifier,
                    'price_type' => $rule->ruleType ? $rule->ruleType->name : null,
                    'update_as' => $rule->ruleValueType ? $rule->ruleValueType->name : null,
                    'value' => $rule->rule_value,
                ];
            })->toArray();

        $availabilityExceptions = collect($this->globalAvailabilityExceptions)
            ->map(function ($rule) {
                /**@var GlobalAvailabilityRule $rule */
                $identifier = $rule->day . $rule->start_time . $rule->end_time . AvailabilityHelper::UN_AVAILABLE;
                return [
                    'day' => $rule->day,
                    'start_time' => $rule->start_time,
                    'end_time' => $rule->end_time,
                    'type' => AvailabilityHelper::UN_AVAILABLE,
                    'identifier' => $identifier
                ];
            })->toArray();

        return array_merge($availabilityRules, $availabilityExceptions);
    }

    public function getLocalRules()
    {
        $availabilityRules = collect($this->availabilityRules)
            ->map(function ($rule) {
                /**@var AvailabilityRule $rule */
                $identifier = $rule->start_time . $rule->end_time . AvailabilityHelper::AVAILABLE . $rule->date;
                return [
                    'date' => $rule->date,
                    'start_time' => $rule->start_time,
                    'end_time' => $rule->end_time,
                    'type' => AvailabilityHelper::AVAILABLE,
                    'identifier' => $identifier,
                    'price_type' => $rule->ruleType ? $rule->ruleType->name : null,
                    'update_as' => $rule->ruleValueType ? $rule->ruleValueType->name : null,
                    'value' => $rule->rule_value,
                ];
            })->toArray();

        $availabilityExceptions = collect($this->availabilityExceptions)
            ->map(function ($rule) {
                /**@var AvailabilityException $rule */
                $identifier = $rule->start_time . $rule->end_time . AvailabilityHelper::UN_AVAILABLE . $rule->date;
                return [
                    'date' => $rule->date,
                    'start_time' => $rule->start_time,
                    'end_time' => $rule->end_time,
                    'type' => AvailabilityHelper::UN_AVAILABLE,
                    'identifier' => $identifier
                ];
            })->toArray();

        return array_merge($availabilityRules, $availabilityExceptions);
    }
}
