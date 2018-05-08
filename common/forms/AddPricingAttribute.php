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
    public $service_id;
    public $group_name;
    public $group_id;

    public function rules()
    {
        return [
            ['group_name', 'safe'],
            ['service_attributes', 'safe'],
            ['service_id', 'integer'],
            ['service_id', 'exist', 'targetClass' => Service::className(), 'targetAttribute' => ['service_id' => 'id']],
            ['group_id', 'integer']
        ];
    }

    public function addPricingGroup()
    {
        if (!$this->validate()) {
            return false;
        }

        $this->service_attributes = json_decode($this->service_attributes, true);

        $service = Service::findOne($this->service_id);
        if (!$service) {
            throw new NotFoundHttpException();
        }

        if (count($this->service_attributes) === 0) {
            throw new NotFoundHttpException();
        }

        if (!empty($this->group_id)) {
            $group = $service->getPricingAttributeGroups()->where(['id' => $this->group_id])->one();
            if (!$group) {
                throw new NotFoundHttpException();
            }
        }else{
            $group = new PricingAttributeGroup();
            $group->name = empty($this->group_name) ? uniqid('PG-', true) : $this->group_name;
        }

        $group->service_id = $this->service_id;
        $group->save();

        foreach ($this->service_attributes as $service_attribute) {
            $this->addPricingAttribute($service_attribute, $group);
        }

        return true;
    }

    public function addPricingAttribute($service_attribute_id, $group)
    {
        $pricingAttribute = PricingAttribute::find()
            ->where(['service_attribute_id' => $service_attribute_id])
            ->one();

        if (!$pricingAttribute) {
            // if not add one..
            $pricingAttribute = new PricingAttribute();
            $pricingAttribute->service_attribute_id = $service_attribute_id;
            return false;
        }

        $pricingAttribute->pricing_attribute_group_id = $group->id;
        return $pricingAttribute->save();
    }
}