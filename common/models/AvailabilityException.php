<?php

namespace common\models;

use Yii;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "availability_exception".
 *
 * @property int $id
 * @property int $calendar_id
 * @property int $start_time
 * @property int $end_time
 * @property string $date
 *
 * @property Calendar $calendar
 */
class AvailabilityException extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'availability_exception';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['calendar_id', 'start_time', 'end_time'], 'integer'],
            [['date'], 'string', 'max' => 255],
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
            'date' => 'Date',
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
     * @param $calendar_id string
     * @throws NotFoundHttpException
     */
    public static function addRules($rules, $calendar_id)
    {
        foreach ($rules as $rule) {

            $availabilityRule = AvailabilityException::find()
                ->where(['start_time' => $rule['start_time']])
                ->andWhere(['end_time' => $rule['end_time']])
                ->andWhere(['date' => $rule['date']])
                ->andWhere(['calendar_id' => $calendar_id])
                ->one();


            if ($availabilityRule) {
                continue;
            }

            $availabilityException = new AvailabilityException();
            $availabilityException->start_time = $rule['start_time'];
            $availabilityException->end_time = $rule['end_time'];
            $availabilityException->date = date('Y-m-d', strtotime($rule['date']));
            $availabilityException->calendar_id = $calendar_id;
            $availabilityException->save();
        }
    }
}
