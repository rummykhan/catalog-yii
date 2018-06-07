<?php
/**
 * Created by PhpStorm.
 * User: rummykhan
 * Date: 4/30/18
 * Time: 11:43 AM
 */

namespace common\forms;


use common\models\ProvidedServiceArea;
use common\models\ServiceArea;
use common\models\ServiceAreaCoverage;
use common\models\ProvidedRequestType;
use RummyKhan\Collection\Arr;
use yii\base\Model;

class AddCoverageArea extends Model
{
    public $provided_service_id;
    public $provided_request_type_id;
    public $provided_service_area_id;
    public $area_name;
    public $coverage_areas;

    public $city_id;

    /**
     * @var ProvidedServiceArea $provided_service_area
     */
    public $provided_service_area = null;

    /**
     * @var ProvidedRequestType $provided_request_type
     */
    public $provided_request_type = null;

    /**
     * @var ServiceArea $service_area
     */
    public $service_area = null;

    public function rules()
    {
        return [
            [['provided_service_id', 'provided_request_type_id'], 'required'],
            [['area_name', 'coverage_areas'], 'safe'],
            [['provided_service_area_id', 'city_id'], 'integer'],
        ];
    }

    public function attach()
    {
        $transaction = \Yii::$app->getDb()->beginTransaction();
        try {
            $this->attachSafe();
            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
        }

        return false;
    }

    public function attachSafe()
    {
        if (!$this->validate()) {
            return false;
        }

        $this->provided_request_type = ProvidedRequestType::find()
            ->where(['provided_service_id' => $this->provided_service_id])
            ->andWhere(['id' => $this->provided_request_type_id])
            ->one();

        // handle provided service area first..
        $this->provided_service_area = null;
        if (!empty($this->provided_service_area_id)) {
            $this->provided_service_area = $this->provided_request_type->getProvidedServiceAreas()->where(['id' => $this->provided_service_area_id])->one();
            $this->service_area = $this->provided_service_area->serviceArea;
        } else {
            $this->provided_service_area = new ProvidedServiceArea();
            $this->provided_service_area->provided_request_type_id = $this->provided_request_type->id;
            $this->service_area = new ServiceArea();
            $this->service_area->name = $this->area_name;
            $this->service_area->city_id = $this->city_id;
            $this->service_area->save();


            $this->provided_service_area->service_area_id = $this->service_area->id;
        }
        $this->provided_service_area->save();


        $coverageAreas = $this->parseCoverageAreas();

        // remove the existing one.. because it's neat for now.
        $this->deleteAllCoverageAreas();

        foreach ($coverageAreas as $coverageArea) {
            $this->addCoverageArea($coverageArea);
        }

        return true;
    }

    protected function deleteAllCoverageAreas()
    {
        /** @var ServiceAreaCoverage $providedServiceCoverage */
        foreach ($this->service_area->serviceAreaCoverages as $serviceAreaCoverage) {
            $serviceAreaCoverage->delete();
        }
    }

    public function addCoverageArea($coverageArea)
    {
        $coordinates = Arr::get($coverageArea, 'coordinates');
        $radius = Arr::get($coverageArea, 'radius');

        if (empty($coordinates) || empty($radius)) {
            return false;
        }

        @list($lat, $long) = explode(',', $coordinates);

        if (empty($lat) || empty($long)) {
            return false;
        }

        $providedServiceCoverage = new ServiceAreaCoverage();
        $providedServiceCoverage->lat = $lat;
        $providedServiceCoverage->lng = $long;
        $providedServiceCoverage->radius = $radius;
        $providedServiceCoverage->service_area_id = $this->service_area->id;
        $providedServiceCoverage->save();
    }

    protected function parseCoverageAreas()
    {
        return json_decode($this->coverage_areas, true);
    }
}