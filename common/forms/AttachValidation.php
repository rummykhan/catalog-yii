<?php
/**
 * Created by PhpStorm.
 * User: rummykhan
 * Date: 4/24/18
 * Time: 2:08 PM
 */

namespace common\forms;


use common\models\ServiceAttribute;
use common\models\ServiceAttributeValidation;
use yii\base\Model;
use yii\web\NotFoundHttpException;

class AttachValidation extends Model
{
    public $service_id;
    public $attribute_id;
    public $validations;

    public function rules()
    {
        return [
            [['attribute_id', 'service_id'], 'integer'],
            ['validations', 'each', 'rule' => ['integer']]
        ];
    }

    public function attach()
    {
        $serviceAttribute = ServiceAttribute::findByService($this->service_id, $this->attribute_id);

        if (!$serviceAttribute) {
            throw new NotFoundHttpException();
        }

        foreach ($this->validations as $validation){
            $this->attachValidation($serviceAttribute, $validation);
        }

        return true;
    }

    /**
     * @param $serviceAttribute ServiceAttribute
     * @param $validation integer
     * @return mixed
     */
    public function attachValidation($serviceAttribute, $validation)
    {
        $attachedValidation = $serviceAttribute->getValidations()->where(['id' => $validation])->one();

        if($attachedValidation){
            return $attachedValidation;
        }

        $serviceAttributeValidation = new ServiceAttributeValidation();
        $serviceAttributeValidation->validation_id = $validation;
        $serviceAttributeValidation->service_attribute_id = $serviceAttribute->id;
        $serviceAttributeValidation->save();

        return $serviceAttributeValidation;
    }
}