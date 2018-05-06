<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "global_availability_exception".
 *
 * @property int $id
 * @property int $provided_service_area_id
 * @property int $start_time
 * @property int $end_time
 * @property string $day
 *
 * @property ProvidedServiceArea $providedServiceArea
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
            [['provided_service_area_id', 'start_time', 'end_time'], 'integer'],
            [['day'], 'string', 'max' => 255],
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
            'day' => 'Day',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProvidedServiceArea()
    {
        return $this->hasOne(ProvidedServiceArea::className(), ['id' => 'provided_service_area_id']);
    }

    /**
     * @param $area ProvidedServiceArea
     * @param $rules array
     */
    public static function addRules($area, $rules)
    {
        // delete existing rules..
        GlobalAvailabilityException::deleteAll([
            'provided_service_area_id' => $area->id
        ]);

        foreach ($rules as $rule) {

            $availabilityRule = $area->getGlobalAvailabilityExceptions()
                ->where(['start_time' => $rule['start_time']])
                ->andWhere(['end_time' => $rule['end_time']])
                ->andWhere(['day' => $rule['day']])
                ->one();

            if ($availabilityRule) {
                continue;
            }

            $availabilityRule = new GlobalAvailabilityException();
            $availabilityRule->start_time = $rule['start_time'];
            $availabilityRule->end_time = $rule['end_time'];
            $availabilityRule->day = $rule['day'];
            $availabilityRule->provided_service_area_id = $area->id;
            $availabilityRule->save();
        }
    }
}
