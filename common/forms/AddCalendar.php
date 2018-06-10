<?php
/**
 * Created by PhpStorm.
 * User: rummykhan
 * Date: 6/8/18
 * Time: 4:29 PM
 */

namespace common\forms;


use common\models\Calendar;
use common\models\GlobalAvailabilityException;
use common\models\GlobalAvailabilityRule;
use common\models\Provider;
use RummyKhan\Collection\Arr;
use Yii;
use yii\base\Model;
use yii\db\Exception;

class AddCalendar extends Model
{
    public $name;
    public $globalRules;
    public $dateRules;
    public $providerId;
    public $calendarId;

    public function rules()
    {
        return [
            [['name', 'globalRules', 'dateRules'], 'safe'],
            ['name', 'required'],
            ['providerId', 'exist', 'targetClass' => Provider::className(), 'targetAttribute' => ['providerId' => 'id']],
            ['calendarId', 'exist', 'targetClass' => Calendar::className(), 'targetAttribute' => ['calendarId' => 'id']]
        ];
    }

    /**
     * @return bool|Calendar
     */
    public function update()
    {
        if (!$this->validate()) {
            return false;
        }

        $transaction = Yii::$app->getDb()->beginTransaction();

        try {

            $calendar = $this->getCalendar();

            $calendar->addGlobalRule(json_decode($this->globalRules, true));
            $calendar->addLocalRule(json_decode($this->dateRules, true));

            $transaction->commit();
        } catch (Exception $e) {

            $transaction->rollBack();

            dd($e);
            return false;
        }


        return $calendar;
    }

    /**
     * @return Calendar
     */
    private function getCalendar()
    {
        if (!empty($this->calendarId)) {
            $calendar = Calendar::findOne($this->calendarId);
        } else {
            $calendar = new Calendar();
            $calendar->provider_id = $this->providerId;
        }

        $calendar->name = $this->name;
        $calendar->save();

        return $calendar;
    }
}