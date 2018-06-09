<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "global_availability_exception".
 *
 * @property int $id
 * @property int $calendar_id
 * @property int $start_time
 * @property int $end_time
 * @property string $day
 *
 * @property Calendar $providedServiceArea
 */
class GlobalAvailabilityException extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'global_availability_exception';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['calendar_id', 'start_time', 'end_time'], 'integer'],
            [['day'], 'string', 'max' => 255],
            [['calendar_id'], 'exist', 'skipOnError' => true, 'targetClass' => Calendar::className(), 'targetAttribute' => ['calendar_id' => 'id']],
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
            'day' => 'Day',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCalendar()
    {
        return $this->hasOne(Calendar::className(), ['id' => 'calendar_id']);
    }

    /**
     * @param $rules array
     * @param $calendar_id integer
     */
    public static function addRules($rules, $calendar_id)
    {
        // delete existing rules..
        GlobalAvailabilityException::deleteAll([
            'calendar_id' => $calendar_id
        ]);

        foreach ($rules as $rule) {

            $availabilityRule = GlobalAvailabilityException::find()
                ->where(['start_time' => $rule['start_time']])
                ->andWhere(['end_time' => $rule['end_time']])
                ->andWhere(['day' => $rule['day']])
                ->andWhere(['calendar_id' => $calendar_id])
                ->one();

            if ($availabilityRule) {
                continue;
            }

            $availabilityRule = new GlobalAvailabilityException();
            $availabilityRule->start_time = $rule['start_time'];
            $availabilityRule->end_time = $rule['end_time'];
            $availabilityRule->day = $rule['day'];
            $availabilityRule->calendar_id = $calendar_id;
            $availabilityRule->save();
        }
    }
}
