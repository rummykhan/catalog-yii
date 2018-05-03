<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "availability_rule".
 *
 * @property int $id
 * @property int $provided_service_area_id
 * @property int $start_time
 * @property int $end_time
 * @property int $rule_value
 * @property int $rule_value_type_id
 * @property string $date
 *
 * @property RuleValueType $ruleValueType
 * @property ProvidedServiceArea $providedServiceArea
 */
class AvailabilityRule extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'availability_rule';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['provided_service_area_id', 'start_time', 'end_time', 'rule_value', 'rule_value_type_id'], 'integer'],
            [['date'], 'string', 'max' => 255],
            [['rule_value_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => RuleValueType::className(), 'targetAttribute' => ['rule_value_type_id' => 'id']],
            [['provided_service_area_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProvidedServiceArea::className(), 'targetAttribute' => ['provided_service_area_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'provided_service_area_id' => 'Provided Service Area ID',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'rule_value' => 'Rule Value',
            'rule_value_type_id' => 'Rule Value Type ID',
            'date' => 'Date',
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
    public function getProvidedServiceArea()
    {
        return $this->hasOne(ProvidedServiceArea::className(), ['id' => 'provided_service_area_id']);
    }
}
