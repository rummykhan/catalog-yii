<?php
/**
 * Created by PhpStorm.
 * User: rummykhan
 * Date: 4/22/18
 * Time: 2:00 PM
 */

namespace common\forms;


use common\models\Attribute;
use common\models\Service;
use common\models\ServiceAttribute;
use yii\base\Model;
use yii\web\NotFoundHttpException;

class AttachAttribute extends Model
{
    public $service_id;
    public $attribute_ids;

    public function rules()
    {
        return [
            [['service_id'], 'integer'],
            ['attribute_ids', 'each', 'rule' => ['integer']]
        ];
    }

    public function attach()
    {
        $service = Service::findOne($this->service_id);
        if (!$service) {
            throw new NotFoundHttpException();
        }

        foreach ($this->attribute_ids as $attribute_id) {
            $this->attachAttribute($service, $attribute_id);
        }
    }

    /**
     * @param $service Service
     * @param $attribute_id integer
     * @return Attribute
     */
    protected function attachAttribute($service, $attribute_id)
    {
        /** @var Attribute $attribute */
        $attribute = $service->getServiceAttributes()->where(['id' => $attribute_id])->one();

        if ($attribute) {
            return $attribute;
        }

        $serviceAttribute = new ServiceAttribute();
        $serviceAttribute->attribute_id = $attribute_id;
        $serviceAttribute->service_id = $service->id;
        $serviceAttribute->save();

        return $serviceAttribute->attribute0;
    }
}