<?php

namespace common\models;

use Yii;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "availability_exception".
 *
 * @property int $id
 * @property int $provided_service_area_id
 * @property int $start_time
 * @property int $end_time
 * @property string $date
 *
 * @property ProvidedServiceArea $providedServiceArea
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
            [['provided_service_area_id', 'start_time', 'end_time'], 'integer'],
            [['date'], 'string', 'max' => 255],
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
            'date' => 'Date',
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
     * @param $date string
     * @throws NotFoundHttpException
     */
    public static function addRules($area, $rules, $date)
    {
        // delete existing rules..
        AvailabilityException::deleteAll([
            'provided_service_area_id' => $area->id,
            'date' => $date,
        ]);

        foreach ($rules as $rule) {

            $availabilityRule = $area->getAvailabilityRules()
                ->where(['start_time' => $rule['start_time']])
                ->andWhere(['end_time' => $rule['end_time']])
                ->andWhere(['date' => $rule['date']])
                ->one();


            if ($availabilityRule) {
                continue;
            }

            $availabilityRule = new AvailabilityException();
            $availabilityRule->start_time = $rule['start_time'];
            $availabilityRule->end_time = $rule['end_time'];
            $availabilityRule->date = date('Y-m-d', strtotime($rule['date']));
            $availabilityRule->provided_service_area_id = $area->id;
            $availabilityRule->save();
        }
    }
}
