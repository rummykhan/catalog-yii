<?php
/**
 * Created by PhpStorm.
 * User: rummykhan
 * Date: 6/4/18
 * Time: 1:21 PM
 */

namespace common\helpers;


use common\models\PricingAttribute;
use common\models\Service;
use common\models\ServiceAttribute;
use common\models\ServiceAttributeOption;
use common\models\ServiceAttributeValidation;

class ServiceCopier
{
    /**
     * @var Service $model
     */
    private $model = null;

    /**
     * @var Service $copiedModel
     */
    private $copiedModel = null;

    /**
     * @var array $logs
     */
    private $logs = [
        'service' => [],
        'attributes' => [],
        'options' => [],
        'pricing' => [],
        'validation' => []
    ];

    function __construct($model)
    {
        $this->model = $model;
    }

    private function cleanUp()
    {
        $connection = \Yii::$app->getDb();
        $connection->createCommand('DELETE FROM service_attribute_option_lang where service_attribute_option_id > :service_attribute_option_id', [':service_attribute_option_id' => 50])
            ->execute();
        ServiceAttributeOption::deleteAll(['>', 'id', 50]);
        // ====== //

        ServiceAttributeValidation::deleteAll(['>', 'id', 12]);
        // ====== //

        PricingAttribute::deleteAll(['>', 'id', 9]);
        // ====== //

        $connection->createCommand('DELETE FROM service_attribute_lang where service_attribute_id > :service_attribute_id', [':service_attribute_id' => 9])
            ->execute();
        ServiceAttribute::deleteAll(['>', 'id', 9]);
        // ====== //

        $connection->createCommand('DELETE FROM service_lang where service_id > :service_id', [':service_id' => 4])
            ->execute();
        Service::deleteAll(['>', 'id', 4]);
        // ====== //
    }

    public function copy($name)
    {
        //$this->cleanUp();

        $this->copyService($name);
        $this->copyServiceAttributes();


        return $this->copiedModel;
    }

    private function copyService($name)
    {
        $this->copiedModel = new Service();
        $this->copiedModel->setAttributes($this->model->getAttributes());
        $this->copiedModel->name = $name;
        $this->copiedModel->save();

        $this->logs['service'][] = $this->copiedModel;
    }

    private function copyServiceAttributes()
    {
        /** @var ServiceAttribute $serviceAttribute */
        foreach ($this->model->serviceAttributes as $serviceAttribute) {
            $newAttribute = new ServiceAttribute();
            $newAttribute->setAttributes($serviceAttribute->getAttributes());
            $newAttribute->service_id = $this->copiedModel->id;
            $newAttribute->save();

            $this->logs['attributes'][] = $newAttribute;

            foreach ($serviceAttribute->serviceAttributeOptions as $serviceAttributeOption) {
                $newAttributeOption = new ServiceAttributeOption();
                $newAttributeOption->setAttributes($serviceAttributeOption->getAttributes());
                $newAttributeOption->service_attribute_id = $newAttribute->id;
                $newAttributeOption->save();

                $this->logs['options'][] = $newAttributeOption;
            }

            foreach ($serviceAttribute->pricingAttributes as $pricingAttribute) {
                $newPricingAttribute = new PricingAttribute();
                $newPricingAttribute->setAttributes($pricingAttribute->getAttributes());
                $newPricingAttribute->service_attribute_id = $newAttribute->id;
                $newPricingAttribute->save();

                $this->logs['pricing'][] = $newPricingAttribute;
            }

            foreach ($serviceAttribute->validations as $validation){
                $newValidation = new ServiceAttributeValidation();
                $newValidation->service_attribute_id = $newAttribute->id;
                $newValidation->validation_id = $validation->id;
                $newValidation->save();

                $this->logs['validation'][] = $newValidation;
            }
        }
    }
}