<?php

namespace common\models;

use RummyKhan\Collection\Collection;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "global_availability_rule".
 *
 * @property int $id
 * @property int $calendar_id
 * @property int $start_time
 * @property int $end_time
 * @property int $rule_value
 * @property int $rule_value_type_id
 * @property int $rule_type_id
 * @property string $day
 *
 * @property RuleValueType $ruleValueType
 * @property RuleType $ruleType
 * @property Calendar $calendar
 */
class GlobalAvailabilityRule extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'global_availability_rule';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['start_time', 'end_time', 'rule_value', 'rule_value_type_id', 'rule_type_id'], 'integer'],
            [['day'], 'string', 'max' => 255],
            [['rule_value_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => RuleValueType::className(), 'targetAttribute' => ['rule_value_type_id' => 'id']],
            [['calendar_id'], 'exist', 'skipOnError' => true, 'targetClass' => Calendar::className(), 'targetAttribute' => ['calendar_id' => 'id']],
            ['rule_type_id', 'exist', 'skipOnError' => true, 'targetClass' => RuleType::className(), 'targetAttribute' => ['rule_type_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'calendar_id' => 'Calendar ID',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'rule_value' => 'Rule Value',
            'rule_value_type_id' => 'Rule Value Type ID',
            'rule_type_id' => 'Rule Type ID',
            'day' => 'Day',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRuleValueType()
    {
        return $this->hasOne(RuleValueType::className(), ['id' => 'rule_value_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCalendar()
    {
        return $this->hasOne(Calendar::className(), ['id' => 'calendar_id']);
    }

    public function getRuleType()
    {
        return $this->hasOne(RuleType::className(), ['id' => 'rule_type_id']);
    }

    /**
     * @param $rules Collection
     * @param $calendar_id integer
     * @throws NotFoundHttpException
     */
    public static function addRules($rules, $calendar_id)
    {
        // delete existing rules..
        GlobalAvailabilityRule::deleteAll([
            'calendar_id' => $calendar_id
        ]);

        foreach ($rules as $rule) {

            $ruleType = RuleType::find()
                ->where(['name' => $rule['price_type']])
                ->one();

            $ruleValueType = RuleValueType::find()
                ->where(['name' => $rule['update_as']])
                ->one();

            $availabilityRule = GlobalAvailabilityRule::find()
                ->where(['start_time' => $rule['start_time']])
                ->andWhere(['end_time' => $rule['end_time']])
                ->andWhere(['day' => $rule['day']])
                ->andWhere(['calendar_id' => $calendar_id])
                ->one();

            if ($availabilityRule) {
                continue;
            }

            $availabilityRule = new GlobalAvailabilityRule();
            $availabilityRule->calendar_id = $calendar_id;
            $availabilityRule->start_time = $rule['start_time'];
            $availabilityRule->end_time = $rule['end_time'];
            $availabilityRule->day = $rule['day'];

            if ($ruleType && !empty($rule['value'])) {
                $availabilityRule->rule_type_id = $ruleType->id;
            }

            if ($ruleValueType && !empty($rule['value'])) {
                $availabilityRule->rule_value_type_id = $ruleValueType->id;
            }

            $availabilityRule->rule_value = $rule['value'];
            $availabilityRule->save();
        }
    }
}
