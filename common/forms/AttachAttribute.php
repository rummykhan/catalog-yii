<?php
/**
 * Created by PhpStorm.
 * User: rummykhan
 * Date: 4/22/18
 * Time: 2:00 PM
 */

namespace common\forms;


use common\models\Attribute;
use common\models\InputType;
use common\models\PriceType;
use common\models\PricingAttribute;
use common\models\PricingAttributeGroup;
use common\models\Service;
use common\models\ServiceAttribute;
use common\models\UserInputType;
use yii\base\Model;
use yii\web\NotFoundHttpException;

class AttachAttribute extends Model
{
    public $service_id;
    public $attribute_name;
    public $input_type;
    public $user_input_type;
    public $price_type;
    public $field_type;
    public $min;
    public $max;
    public $service_attribute_id;
    public $pricing_attribute_group_id;
    public $pricing_attribute_id;

    public function rules()
    {
        return [
            [['service_id', 'input_type', 'user_input_type'], 'required'],
            [['service_id', 'input_type', 'user_input_type', 'service_attribute_id', 'min', 'max', 'field_type'], 'integer'],
            [['price_type'], 'integer'],
            [['attribute_name'], 'required'],
            [['attribute_name'], 'safe'],
            ['input_type', 'exist', 'targetClass' => InputType::className(), 'targetAttribute' => ['input_type' => 'id']],
            ['user_input_type', 'exist', 'targetClass' => UserInputType::className(), 'targetAttribute' => ['user_input_type' => 'id']],
            ['price_type', 'exist', 'targetClass' => PriceType::className(), 'targetAttribute' => ['price_type' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'attribute_name' => 'Field name'
        ];
    }

    public function attach()
    {

        dd($this);
        $service = Service::findOne($this->service_id);
        if (!$service) {
            throw new NotFoundHttpException();
        }

        $priceType = PriceType::findOne($this->price_type);

        $serviceAttribute = null;
        if (!empty($this->service_attribute_id)) {
            $serviceAttribute = ServiceAttribute::find()
                ->where(['service_id' => $service->id])
                ->andWhere(['id' => $this->service_attribute_id])
                ->one();
        }

        if (!$serviceAttribute) {
            $serviceAttribute = new ServiceAttribute();
        }

        // create / update service attribute.
        $serviceAttribute->name = $this->attribute_name;
        $serviceAttribute->service_id = $service->id;
        $serviceAttribute->user_input_type_id = $this->user_input_type;
        $serviceAttribute->input_type_id = $this->input_type;
        $serviceAttribute->save();

        $pricingAttributeGroup = null;
        if (!empty($this->pricing_attribute_group_id)) {
            $pricingAttributeGroup = PricingAttributeGroup::find()
                ->where(['service_id' => $service->id])
                ->andWhere(['id' => $this->pricing_attribute_group_id])
                ->one();
        }

        if (empty($pricingAttributeGroup)) {
            $pricingAttributeGroup = new PricingAttributeGroup();
        }

        // create / update attribute group
        $pricingAttributeGroup->name = $service->name . ' ' . $priceType->type;
        $pricingAttributeGroup->service_id = $service->id;
        $pricingAttributeGroup->save();


        $pricingAttribute = null;
        if (!empty($this->pricing_attribute_id)) {
            $pricingAttribute = PricingAttribute::find()
                ->where(['id' => $this->pricing_attribute_id])
                ->andWhere(['service_attribute_id' => $serviceAttribute->id])
                ->one();
        }

        if (!$pricingAttribute) {
            $pricingAttribute = new PricingAttribute();
        }

        $pricingAttribute->service_attribute_id = $serviceAttribute->id;
        $pricingAttribute->price_type_id = $priceType->id;
        $pricingAttribute->pricing_attribute_group_id = $pricingAttributeGroup->id;
        $pricingAttribute->save();

        // check if it's a range attribute add all options



        return $serviceAttribute;
    }
}