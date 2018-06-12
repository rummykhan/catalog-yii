<?php
/**
 * Created by PhpStorm.
 * User: rummykhan
 * Date: 6/12/18
 * Time: 1:23 AM
 */

namespace console\controllers;


use common\models\Category;
use common\models\City;
use common\models\RequestType;
use common\models\Service;
use common\models\ServiceCity;
use common\models\ServiceRequestType;
use common\oldmodels\ServiceCategory;
use yii\console\Controller;
use yii\helpers\Console;

class ServiceController extends Controller
{
    public $map = [];

    public function actionMigrate()
    {
        // Delete all categories..
        \Yii::$app->getDb()->createCommand('DELETE FROM service_lang WHERE id > :id', [':id' => 0])->execute();
        Service::deleteAll(['>', 'id', 0]);
        ServiceRequestType::deleteAll(['>', 'id', 0]);
        ServiceCity::deleteAll(['>', 'id', 0]);

        $this->seedServiceType();

        $oldServices = \common\oldmodels\Service::find();

        /** @var \common\oldmodels\Service $oldService */
        foreach ($oldServices->each(20) as $oldService) {

            $service = $this->createService($oldService);

            $this->map["oldie-" . $oldService->id] = $service;

            $this->stdout(" -> Created " . $oldService->name);
            $this->stdout("\r\n");

        }
    }

    /**
     * @param \common\oldmodels\Service $oldService
     * @return Service
     */
    private function createService($oldService)
    {
        $service = new Service();
        $service->name = $oldService->name;
        $service->description = trim(htmlspecialchars_decode(strip_tags($oldService->description)));
        $service->mobile_description = trim(htmlspecialchars_decode(strip_tags($oldService->mobile_description)));
        $service->active = $oldService->enable;
        $service->save();

        if (!empty($oldService->parent_id)) {
            $service->category_id = $oldService->parent_id;
            $service->save();
        }

        $this->updateRequestTypes($oldService, $service);
        $this->updateCities($oldService, $service);

        return $service;
    }

    /**
     * @param $oldService \common\oldmodels\Service
     * @param $service Service
     * @return mixed
     */
    private function updateRequestTypes($oldService, $service)
    {
        if ($oldService->collect_and_return) {
            $requestType = RequestType::find()->where(['name' => RequestType::TYPE_COLLECT_AND_RETURN])->one();

            if ($requestType) {
                $serviceRequestType = new ServiceRequestType();
                $serviceRequestType->service_id = $service->id;
                $serviceRequestType->request_type_id = $requestType->id;
                $serviceRequestType->save();
            }
        }


        if ($oldService->in_house) {
            $requestType = RequestType::find()->where(['name' => RequestType::TYPE_IN_HOUSE])->one();

            if ($requestType) {
                $serviceRequestType = new ServiceRequestType();
                $serviceRequestType->service_id = $service->id;
                $serviceRequestType->request_type_id = $requestType->id;
                $serviceRequestType->save();
            }
        }

    }

    /**
     * @param $oldService \common\oldmodels\Service
     * @param $service Service
     * @return mixed
     */
    private function updateCities($oldService, $service)
    {
        if (empty($oldService->available_cities)) {
            return true;
        }

        $cities = explode(',', $oldService->available_cities);

        if (empty($cities)) {
            return true;
        }

        foreach ($cities as $city) {
            $dbCity = City::find()->where(['name' => CategoryController::$cityMap[$city]])->one();

            if (!$dbCity) {
                dd($city);
                continue;
            }

            $serviceCity = new ServiceCity();
            $serviceCity->service_id = $service->id;
            $serviceCity->city_id = $dbCity->id;
            $serviceCity->save();
        }

        return true;
    }

    protected function seedServiceType()
    {
        $this->stdout('Seeding service type', Console::FG_GREEN);
        $this->stdout("\n");
        if (RequestType::find()->count() > 0) {
            RequestType::deleteAll();
        }

        $options = [
            RequestType::TYPE_IN_HOUSE,
            RequestType::TYPE_COLLECT_AND_RETURN,
            RequestType::TYPE_WALK_IN
        ];
        foreach ($options as $option) {
            $attributeOption = new RequestType();
            $attributeOption->name = $option;
            $attributeOption->save();
        }
    }
}