<?php

namespace common\models;

use frontend\controllers\ProvidedServiceAreaController;
use RummyKhan\Collection\Arr;
use Yii;

/**
 * This is the model class for table "service_area".
 *
 * @property int $id
 * @property string $name
 * @property int $city_id
 * @property int $provider_id
 *
 * @property ProvidedServiceArea[] $providedServiceAreas
 * @property Provider $provider
 * @property City $city
 * @property ServiceAreaCoverage[] $serviceAreaCoverages
 *
 */
class ServiceArea extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'service_area';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['city_id', 'provider_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['provider_id'], 'exist', 'skipOnError' => true, 'targetClass' => Provider::className(), 'targetAttribute' => ['provider_id' => 'id']],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => City::className(), 'targetAttribute' => ['city_id' => 'id']],
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
            'city_id' => 'City ID',
            'provider_id' => 'Provider ID',
        ];
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
    public function getCity()
    {
        return $this->hasOne(City::className(), ['id' => 'city_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceAreaCoverages()
    {
        return $this->hasMany(ServiceAreaCoverage::className(), ['service_area_id' => 'id']);
    }

    /**
     * Delete all coverages in a service area
     */
    public function deleteAllAreas()
    {
        ServiceAreaCoverage::deleteAll(['service_area_id' => $this->id]);
    }

    /**
     * @param $areas array
     */
    public function addAreas($areas)
    {
        foreach ($areas as $area) {
            $this->addArea($area);
        }
    }

    public function addArea($area)
    {
        $coordinates = Arr::get($area, 'coordinates');
        $radius = Arr::get($area, 'radius');

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
        $providedServiceCoverage->service_area_id = $this->id;
        $providedServiceCoverage->save();
    }
}
