<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "rule_type".
 *
 * @property int $id
 * @property string $name
 *
 * @property AvailabilityRule[] $availabilityRules
 * @property GlobalAvailabilityRule[] $globalAvailabilityRules
 */
class RuleType extends \yii\db\ActiveRecord
{
    const TYPE_INCREASE = 'Increase';
    const TYPE_DECREASE = 'Decrease';

    const TYPE_AVAILABLE = 'Available';
    const TYPE_NOT_AVAILABLE = 'Not Available';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rule_type';
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
        return $this->hasMany(AvailabilityRule::className(), ['rule_type_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGlobalAvailabilityRules()
    {
        return $this->hasMany(GlobalAvailabilityRule::className(), ['rule_type_id' => 'id']);
    }
}
