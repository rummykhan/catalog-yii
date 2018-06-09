<?php

namespace common\models;

use Yii;

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
        if (empty($rules)) {
            return false;
        }

        $rulesJson = collect($rules)->groupBy('type')->toArray();

        foreach ($rulesJson as $ruleType => $rules) {
            switch ($ruleType) {
                case 'Available':
                    GlobalAvailabilityRule::addRules($rules, $this->id);
                    break;

                case 'Not Available':
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
        if (empty($rules)) {
            return false;
        }

        $rulesJson = collect($rules)->groupBy('type')->toArray();

        foreach ($rulesJson as $ruleType => $rules) {
            switch ($ruleType) {
                case 'Available':
                    AvailabilityRule::addRules($rules, $this->id);
                    break;

                case 'Not Available':
                    AvailabilityException::addRules($rules, $this->id);
                    break;
            }
        }

        return true;
    }
}
