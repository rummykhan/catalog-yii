<?php
/**
 * Created by PhpStorm.
 * User: rummykhan
 * Date: 4/23/18
 * Time: 5:21 PM
 */

namespace common\forms;


use common\models\PriceType;
use common\models\PricingAttribute;
use common\models\PricingAttributeGroup;
use common\models\Service;
use yii\base\Model;
use yii\web\NotFoundHttpException;

class AddPricingAttribute extends Model
{
    public $service_attributes;
    public $price_type_id;
    public $service_id;
    public $group_name;

    public function rules()
    {
        return [
            [['service_attributes'], 'each', 'rule' => ['integer']],
            ['price_type_id', 'integer'],
        ];
    }

    public function addAttribute()
    {
        $service = Service::findOne($this->service_id);
        if (!$service) {
            throw new NotFoundHttpException();
        }

        $priceType = PriceType::find()->where(['id' => $this->price_type_id])->one();
        if (!$priceType) {
            throw new NotFoundHttpException();
        }

        $group = new PricingAttributeGroup();
        $group->name = empty($this->group_name) ? $priceType->type : $this->group_name;
        $group->service_id = $this->service_id;
        $group->save();

        foreach ($this->service_attributes as $service_attribute) {
            $this->addPricingAttribute($service_attribute, $group);
        }

        return true;
    }

    public function addPricingAttribute($service_attribute_id, $group)
    {
        $pricingAttribute = PricingAttribute::find()->where(['service_attribute_id' => $service_attribute_id])->one();

        if ($pricingAttribute) {
            // TODO: What to do if the pricing attribute already exists.
            return $pricingAttribute;
        }

        $pricingAttribute = new PricingAttribute();
        $pricingAttribute->service_attribute_id = $service_attribute_id;
        $pricingAttribute->price_type_id = $this->price_type_id;
        $pricingAttribute->pricing_attribute_group_id = $group->id;
        $pricingAttribute->save();
    }
}