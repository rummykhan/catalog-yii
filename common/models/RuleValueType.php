<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "rule_value_type".
 *
 * @property int $id
 * @property string $name
 *
 * @property AvailabilityRule[] $availabilityRules
 * @property GlobalAvailabilityRule[] $globalAvailabilityRules
 */
class RuleValueType extends \yii\db\ActiveRecord
{
    const TYPE_PERCENTAGE = 'Percentage';
    const TYPE_FIXED = 'Fixed';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rule_value_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAvailabilityRules()
    {
        return $this->hasMany(AvailabilityRule::className(), ['rule_value_type_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGlobalAvailabilityRules()
    {
        return $this->hasMany(GlobalAvailabilityRule::className(), ['rule_value_type_id' => 'id']);
    }

    public static function toList()
    {
        return collect(static::find()->all())->pluck('name','name')->toArray();
    }
}
