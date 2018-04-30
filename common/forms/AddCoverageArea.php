<?php
/**
 * Created by PhpStorm.
 * User: rummykhan
 * Date: 4/30/18
 * Time: 11:43 AM
 */

namespace common\forms;


use RummyKhan\Collection\Arr;
use yii\base\Model;

class AddCoverageArea extends Model
{
    public $provided_service_id;
    public $service_type;
    public $area_name;
    public $area_id;
    public $coverage_areas;

    public function rules()
    {
        return [
            [['provided_service_id', 'service_type'], 'required'],
            [['area_name', 'coverage_areas'], 'safe'],
            [['area_id'], 'integer'],
        ];
    }

    public function attach()
    {
        if (!$this->validate()) {
            return false;
        }


        $coverageAreas = $this->parseCoverageAreas();

        $this->deleteAllCoverageAreas();
        foreach ($coverageAreas as $coverageArea) {
            $this->addCoverageArea($coverageArea);
        }

        dd($this, $coverageAreas);

        return true;
    }

    protected function deleteAllCoverageAreas()
    {

    }

    public function addCoverageArea($coverageArea)
    {
        $coordinates = Arr::get($coverageArea, 'coordinates');
        $radius = Arr::get($coverageArea, 'radius');

        if (empty($coordinates) || empty($radius)) {
            return false;
        }
    }

    protected function parseCoverageAreas()
    {
        return json_decode($this->coverage_areas, true);
    }
}