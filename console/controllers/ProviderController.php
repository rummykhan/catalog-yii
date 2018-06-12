<?php
/**
 * Created by PhpStorm.
 * User: rummykhan
 * Date: 6/12/18
 * Time: 10:38 AM
 */

namespace console\controllers;


use common\models\Calendar;
use common\models\Country;
use common\models\GlobalAvailabilityException;
use common\models\GlobalAvailabilityRule;
use common\models\ProvidedServiceType;
use common\models\Provider;
use common\models\RequestType;
use common\models\ServiceArea;
use common\models\ServiceAreaCoverage;
use common\models\ServiceRequestType;
use common\oldmodels\ProvidedService;
use common\oldmodels\ProviderCoveredArea;
use yii\console\Controller;

class ProviderController extends Controller
{

    public function actionMigrate()
    {
        Provider::deleteAll(['>', 'id', 0]);

        $oldProviders = \common\oldmodels\Provider::find();

        /** @var \common\oldmodels\Provider $oldProvider */
        foreach ($oldProviders->each(20) as $oldProvider) {

            $provider = $this->createProvider($oldProvider);

            if (empty($provider) || empty($provider->id)) {
                continue;
            }

            $this->addCoverageArea($provider, $oldProvider->getProviderCoveredAreas()->asArray()->all());

            $calendar = $this->addCalendar($provider, $oldProvider->schedule);

            $this->provideService($provider, $oldProvider->providedServices, $calendar);

            $this->stdout(" -> Provider Created " . $provider->username);
            $this->stdout("\r\n");
        }
    }

    /**
     * @param $oldProvider \common\oldmodels\Provider
     * @return Provider
     */
    private function createProvider($oldProvider)
    {
        $provider = new Provider();
        $provider->first_name = $oldProvider->firstname;
        $provider->last_name = $oldProvider->lastname;
        $provider->username = $oldProvider->username;
        $provider->email = $oldProvider->email;
        $provider->password = \Yii::$app->getSecurity()->generatePasswordHash('12345');

        if (!empty($oldProvider->country)) {
            $country = Country::find()->where(['name' => CategoryController::$countryMap[$oldProvider->country]])->one();

            if ($country) {
                $provider->country_id = $country->id;
            }
        }
        $provider->status = $oldProvider->status;
        $provider->save();

        return $provider;
    }

    /**
     * @param $provider Provider
     * @param $coveredAreas ProviderCoveredArea[]
     * @return mixed
     */
    private function addCoverageArea($provider, $coveredAreas)
    {

        $coveredAreaGroup = collect($coveredAreas)->groupBy('type')->toArray();

        /**
         * @var string $group
         * @var  $areas ProviderCoveredArea[]
         */
        foreach ($coveredAreaGroup as $group => $areas) {

            $serviceArea = new ServiceArea();
            $serviceArea->name = empty($group) ? $provider->username : $group;
            $serviceArea->provider_id = $provider->id;
            $serviceArea->save();

            if (empty($coveredAreas)) {
                return $serviceArea;
            }

            /** @var ProviderCoveredArea $coveredArea */
            foreach ($areas as $coveredArea) {

                @list($lat, $lng) = explode(',', $coveredArea['coordinates']);

                $found = ServiceAreaCoverage::find()
                    ->where(['lat' => $lat])
                    ->andWhere(['lng' => $lng])
                    ->andWhere(['service_area_id' => $serviceArea->id])
                    ->one();

                if ($found) {
                    continue;
                }

                $coverage = new ServiceAreaCoverage();
                $coverage->service_area_id = $serviceArea->id;
                $coverage->lat = $lat;
                $coverage->lng = $lng;
                $coverage->radius = $coveredArea['radius'];
                $coverage->save();
            }
        }
    }

    private function addCalendar($provider, $schedule)
    {
        $calendar = new Calendar();
        $calendar->name = $provider->username;
        $calendar->provider_id = $provider->id;
        $calendar->save();

        $globalAvailabilityRule = new GlobalAvailabilityRule();
        $globalAvailabilityRule->calendar_id = $calendar->id;
        $globalAvailabilityRule->start_time = 9;
        $globalAvailabilityRule->end_time = 18;
        $globalAvailabilityRule->day = 'All';
        $globalAvailabilityRule->save();

        $globalExceptionRule = new GlobalAvailabilityException();
        $globalExceptionRule->calendar_id = $calendar->id;
        $globalExceptionRule->day = 'Fri';
        $globalExceptionRule->start_time = 0;
        $globalExceptionRule->end_time = 23;
        $globalExceptionRule->save();

        return $calendar;
    }

    /**
     * @param $provider Provider
     * @param $oldProvidedServices ProvidedService[]
     * @param $calendar Calendar
     * @return mixed
     */
    private function provideService($provider, $oldProvidedServices, $calendar)
    {
        if (count($oldProvidedServices) === 0) {
            return true;
        }

        /** @var ProvidedService $oldProvidedService */
        foreach ($oldProvidedServices as $oldProvidedService) {

            $providedService = new \common\models\ProvidedService();
            $providedService->service_id = $oldProvidedService->service_id;
            $providedService->provider_id = $provider->id;
            $providedService->save();

            $inHouseArea = ServiceArea::find()
                ->where(['provider_id' => $provider->id])
                ->andWhere(['name' => 'in_house'])
                ->one();

            $collectAndReturnArea = ServiceArea::find()
                ->where(['provider_id' => $provider->id])
                ->andWhere(['name' => 'collect_and_return'])
                ->one();

            $area = ServiceArea::find()
                ->where(['provider_id' => $provider->id])
                ->one();

            if (!$inHouseArea) {
                $inHouseArea = $area;
            }

            if (!$collectAndReturnArea) {
                $collectAndReturnArea = $area;
            }


            $typeInHouse = RequestType::find()->where(['name' => RequestType::TYPE_IN_HOUSE])->one();
            $typeCollect = RequestType::find()->where(['name' => RequestType::TYPE_COLLECT_AND_RETURN])->one();

            if ($oldProvidedService->in_house) {

                $serviceRequestType = ServiceRequestType::find()
                    ->where(['service_id' => $providedService->service_id])
                    ->andWhere(['request_type_id' => $typeInHouse->id])
                    ->one();

                if ($serviceRequestType && $calendar && $inHouseArea) {
                    $providedServiceType = new ProvidedServiceType();
                    $providedServiceType->provided_service_id = $providedService->id;
                    $providedServiceType->calendar_id = $calendar->id;
                    $providedServiceType->service_area_id = $inHouseArea->id;
                    $providedServiceType->service_request_type_id = $serviceRequestType->id;
                    $providedServiceType->save();
                }

            }


            if ($oldProvidedService->collect_and_return) {

                $serviceRequestType = ServiceRequestType::find()
                    ->where(['service_id' => $providedService->service_id])
                    ->andWhere(['request_type_id' => $typeCollect->id])
                    ->one();

                if ($serviceRequestType && $calendar && $collectAndReturnArea) {
                    $providedServiceType = new ProvidedServiceType();
                    $providedServiceType->provided_service_id = $providedService->id;
                    $providedServiceType->calendar_id = $calendar->id;
                    $providedServiceType->service_area_id = $collectAndReturnArea->id;
                    $providedServiceType->service_request_type_id = $serviceRequestType->id;
                    $providedServiceType->save();
                }
            }

        }
    }
}