<?php
/**
 * Created by PhpStorm.
 * User: rummykhan
 * Date: 4/30/18
 * Time: 11:43 AM
 */

namespace common\forms;


use common\models\City;
use common\models\Provider;
use common\models\ServiceArea;
use common\models\ServiceAreaCoverage;
use common\models\ProvidedRequestType;
use RummyKhan\Collection\Arr;
use yii\base\Model;

class AddCoverageArea extends Model
{
    public $area_name;
    public $coverage_areas;
    public $provider_id;
    public $city_id;
    public $service_area_id;

    public function rules()
    {
        return [
            [['provider_id', 'area_name'], 'required'],
            [['area_name', 'coverage_areas'], 'safe'],
            [['provider_id', 'city_id', 'service_area_id'], 'integer'],
            ['provider_id', 'exist', 'skipOnError' => true, 'targetClass' => Provider::className(), 'targetAttribute' => ['provider_id' => 'id']],
            ['city_id', 'exist', 'skipOnError' => true, 'targetClass' => City::className(), 'targetAttribute' => ['city_id' => 'id']],
            ['service_area_id', 'exist', 'skipOnError' => true, 'targetClass' => ServiceArea::className(), 'targetAttribute' => ['service_area_id' => 'id']]
        ];
    }

    /**
     * @return bool|ServiceArea
     */
    public function createOrUpdate()
    {
        if (!$this->validate()) {
            return false;
        }

        $transaction = \Yii::$app->getDb()->beginTransaction();
        try {

            $serviceArea = $this->getServiceArea();

            $serviceArea->deleteAllAreas();

            $serviceArea->addAreas($this->parseCoverageAreas());

            $transaction->commit();

        } catch (\Exception $e) {
            $transaction->rollBack();

            dd($e);
            return false;
        }


        return $serviceArea;
    }

    /**
     * @return ServiceArea|null
     */
    protected function getServiceArea()
    {
        if (empty($this->service_area_id)) {
            $serviceArea = new ServiceArea();
            $serviceArea->name = $this->area_name;
            $serviceArea->city_id = $this->city_id;
            $serviceArea->provider_id = $this->provider_id;
            $serviceArea->save();
        } else {
            $serviceArea = ServiceArea::findOne($this->service_area_id);
            $serviceArea->name = $this->area_name;
            $serviceArea->city_id = $this->city_id;
            $serviceArea->save();
        }

        return $serviceArea;
    }

    protected function parseCoverageAreas()
    {
        return json_decode($this->coverage_areas, true);
    }
}