<?php
/**
 * Created by PhpStorm.
 * User: rummykhan
 * Date: 4/30/18
 * Time: 11:13 AM
 */

namespace common\forms;


use common\models\ProvidedService;
use common\models\ProvidedRequestType;
use yii\base\Model;

class AddType extends Model
{
    public $provided_service_id;
    public $service_types;

    /**
     * @var ProvidedService $provided_service
     */
    public $provided_service;

    public function rules()
    {
        return [
            [['service_types'], 'each', 'rule' => ['integer']],
            [['provided_service_id'], 'integer'],
        ];
    }

    public function attach()
    {
        if (!$this->validate()) {
            return false;
        }

        $this->provided_service = ProvidedService::findOne($this->provided_service_id);

        if (!$this->provided_service) {
            return false;
        }


        foreach ($this->service_types as $service_type) {
            $this->attachServiceType($service_type);
        }

        return true;
    }

    public function attachServiceType($service_type)
    {
        $type = ProvidedRequestType::find()
            ->where(['provided_service_id' => $this->provided_service_id])
            ->andWhere(['service_type_id' => $service_type])
            ->one();

        if ($type) {
            return false;
        }

        $type = new ProvidedRequestType();
        $type->provided_service_id = $this->provided_service->id;
        $type->service_type_id = $service_type;
        $type->save();
    }

    public function updateServiceTypes()
    {
        $this->provided_service = ProvidedService::findOne($this->provided_service_id);

        if (!$this->provided_service) {
            return null;
        }

        $this->service_types = collect($this->provided_service->getProvidedServiceTypes()->all())->pluck('id')->toArray();
    }
}