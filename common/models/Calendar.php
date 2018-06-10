<?php

namespace common\models;

use common\helpers\AvailabilityHelper;
use Yii;
use yii\rbac\Rule;

/**
 * This is the model class for table "calendar".
 *
 * @property int $id
 * @property string $name
 * @property int $provider_id
 * @property int $deleted
 *
 * @property Provider $provider
 * @property AvailabilityException[] $availabilityExceptions
 * @property AvailabilityRule[] $availabilityRules
 * @property GlobalAvailabilityException[] $globalAvailabilityExceptions
 * @property GlobalAvailabilityRule[] $globalAvailabilityRules
 */
class Calendar extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'calendar';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['provider_id', 'deleted'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['provider_id'], 'exist', 'skipOnError' => true, 'targetClass' => Provider::className(), 'targetAttribute' => ['provider_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'provider_id' => 'Provider ID',
            'deleted' => 'Deleted',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAvailabilityExceptions()
    {
        return $this->hasMany(AvailabilityException::className(), ['calendar_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAvailabilityRules()
    {
        return $this->hasMany(AvailabilityRule::className(), ['calendar_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProvider()
    {
        return $this->hasOne(Provider::className(), ['id' => 'provider_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGlobalAvailabilityExceptions()
    {
        return $this->hasMany(GlobalAvailabilityException::className(), ['calendar_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGlobalAvailabilityRules()
    {
        return $this->hasMany(GlobalAvailabilityRule::className(), ['calendar_id' => 'id']);
    }

    /**
     * @param $rules array
     * @return bool
     */
    public function addGlobalRule($rules)
    {
        // Remove old rules..
        GlobalAvailabilityRule::deleteAll(['calendar_id' => $this->id]);
        GlobalAvailabilityException::deleteAll(['calendar_id' => $this->id]);

        $rulesJson = collect($rules)->groupBy('type')->toArray();

        foreach ($rulesJson as $ruleType => $rules) {

            switch ($ruleType) {
                case RuleType::TYPE_AVAILABLE:
                    GlobalAvailabilityRule::addRules($rules, $this->id);
                    break;

                case RuleType::TYPE_NOT_AVAILABLE:
                    GlobalAvailabilityException::addRules($rules, $this->id);
                    break;
            }
        }

        return true;
    }

    /**
     * @param $rules
     * @return bool
     */
    public function addLocalRule($rules)
    {
        AvailabilityRule::deleteAll(['calendar_id' => $this->id,]);
        AvailabilityException::deleteAll(['calendar_id' => $this->id]);

        $rulesJson = collect($rules)->groupBy('type')->toArray();

        foreach ($rulesJson as $ruleType => $rules) {
            switch ($ruleType) {
                case RuleType::TYPE_AVAILABLE:
                    AvailabilityRule::addRules($rules, $this->id);
                    break;

                case RuleType::TYPE_NOT_AVAILABLE:
                    AvailabilityException::addRules($rules, $this->id);
                    break;
            }
        }

        return true;
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
