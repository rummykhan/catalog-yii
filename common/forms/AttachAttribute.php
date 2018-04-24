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
    public $attribute_id;
    public $input_type;
    public $user_input_type;

    public function rules()
    {
        return [
            [['service_id', 'input_type', 'user_input_type', 'attribute_id'], 'integer']
        ];
    }

    public function attach()
    {
        $service = Service::findOne($this->service_id);
        if (!$service) {
            throw new NotFoundHttpException();
        }

        /** @var Attribute $attribute */
        $attribute = $service->getServiceAttributes()->where(['id' => $this->attribute_id])->one();

        if ($attribute) {
            return $attribute;
        }

        $serviceAttribute = new ServiceAttribute();
        $serviceAttribute->attribute_id = $this->attribute_id;
        $serviceAttribute->service_id = $service->id;
        $serviceAttribute->user_input_type_id = $this->user_input_type;
        $serviceAttribute->input_type_id = $this->input_type;
        $serviceAttribute->save();

        return $serviceAttribute->attribute0;
    }
}