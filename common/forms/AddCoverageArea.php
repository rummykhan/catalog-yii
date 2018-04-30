<?php
/**
 * Created by PhpStorm.
 * User: rummykhan
 * Date: 4/30/18
 * Time: 11:43 AM
 */

namespace common\forms;


use common\models\ProvidedServiceArea;
use common\models\ProvidedServiceCoverage;
use common\models\ProvidedServiceType;
use RummyKhan\Collection\Arr;
use yii\base\Model;

class AddCoverageArea extends Model
{
    public $provided_service_id;
    public $service_type;
    public $area_name;
    public $area_id;
    public $coverage_areas;

    public $city_id;

    /**
     * @var ProvidedServiceArea $provided_service_area
     */
    public $provided_service_area;

    /**
     * @var ProvidedServiceType $provided_service_type
     */
    public $provided_service_type;

    public function rules()
    {
        return [
            [['provided_service_id', 'service_type'], 'required'],
            [['area_name', 'coverage_areas'], 'safe'],
            [['area_id', 'city_id'], 'integer'],
        ];
    }

    public function attach()
    {
        if (!$this->validate()) {
            return false;
        }

        $this->provided_service_type = ProvidedServiceType::find()
            ->where(['provided_service_id' => $this->provided_service_id])
            ->andWhere(['service_type_id' => $this->service_type])
            ->one();

        // handle provided service area first..
        $this->provided_service_area = null;
        if (!empty($this->area_id)) {
            $this->provided_service_area = $this->provided_service_type->getProvidedServiceAreas()->where(['id' => $this->area_id])->one();
        } else {
            $this->provided_service_area = new ProvidedServiceArea();
            $this->provided_service_area->provided_service_type_id = $this->provided_service_type->id;
        }

        $this->provided_service_area->name = $this->area_name;
        $this->provided_service_area->city_id = $this->city_id;
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
        /** @var ProvidedServiceCoverage $providedServiceCoverage */
        foreach ($this->provided_service_area->providedServiceCoverages as $providedServiceCoverage) {
            $providedServiceCoverage->delete();
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

        $providedServiceCoverage = new ProvidedServiceCoverage();
        $providedServiceCoverage->lat = $lat;
        $providedServiceCoverage->lng = $long;
        $providedServiceCoverage->radius = $radius;
        $providedServiceCoverage->provided_service_area_id = $this->provided_service_area->id;
        $providedServiceCoverage->save();
    }

    protected function parseCoverageAreas()
    {
        return json_decode($this->coverage_areas, true);
    }
}