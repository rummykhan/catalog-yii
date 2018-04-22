<?php
/**
 * Created by PhpStorm.
 * User: rummykhan
 * Date: 4/22/18
 * Time: 2:36 PM
 */

namespace common\forms;


use common\models\AttributeOption;
use common\models\Service;
use common\models\ServiceAttribute;
use common\models\ServiceAttributeOption;
use yii\base\Model;
use yii\web\NotFoundHttpException;

class AttachOption extends Model
{
    public $service_id;
    public $attribute_id;
    public $options_ids;

    public function rules()
    {
        return [
            [['service_id', 'attribute_id'], 'integer'],
            ['options_ids', 'each', 'rule' => ['integer']]
        ];
    }

    public function attach()
    {
        $serviceAttribute = ServiceAttribute::find()
            ->where(['service_id' => $this->service_id])
            ->andWhere(['attribute_id' => $this->attribute_id])
            ->one();

        if (!$serviceAttribute) {
            throw new NotFoundHttpException();
        }

        foreach ($this->options_ids as $options_id){
            $this->attachOption($serviceAttribute, $options_id);
        }

        return true;
    }

    /**
     * @param $attribute ServiceAttribute
     * @param $option_id integer
     * @throws NotFoundHttpException
     * @return mixed
     */
    protected function attachOption($attribute, $option_id)
    {
        $attributeOption = AttributeOption::findOne($option_id);

        if(!$attributeOption){
            throw new NotFoundHttpException();
        }

        $existingOption = $attribute->getServiceAttributeOptions()->where(['attribute_option_id' => $attributeOption->id])->one();

        if($existingOption){
            return $existingOption;
        }

        $option = new ServiceAttributeOption();
        $option->service_attribute_id = $attribute->id;
        $option->attribute_option_id = $option_id;
        $option->save();

        return $option;

    }
}