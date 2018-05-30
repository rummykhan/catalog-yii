<?php
/**
 * Created by PhpStorm.
 * User: rummykhan
 * Date: 5/21/18
 * Time: 10:10 AM
 */

namespace common\forms;


use common\models\Service;
use common\models\ServiceAttribute;
use common\models\ServiceAttributeOption;
use common\models\ServiceCompositeAttribute;
use common\models\ServiceCompositeAttributeChild;
use common\models\ServiceCompositeAttributeParent;
use RummyKhan\Collection\Arr;
use yii\base\Model;

class AddDependency extends Model
{
    private $_data;

    public $parent;
    public $parent_options;

    public $child;
    public $child_options;

    public $service_id;

    public function rules()
    {
        return [
            [['parent', 'child', 'service_id', 'parent_options', 'child_options'], 'required'],
            [['parent', 'child', 'service_id'], 'integer'],
            ['service_id', 'exist', 'targetClass' => Service::className(), 'targetAttribute' => ['service_id' => 'id']],
            ['parent_options', 'each', 'rule' => ['exist', 'targetClass' => ServiceAttributeOption::className(), 'targetAttribute' => ['parent_options' => 'id']]],
            ['child_options', 'each', 'rule' => ['exist', 'targetClass' => ServiceAttributeOption::className(), 'targetAttribute' => ['child_options' => 'id']]],
            [['parent'], 'exist', 'targetClass' => ServiceAttribute::className(), 'targetAttribute' => ['parent' => 'id']],
            [['child'], 'exist', 'targetClass' => ServiceAttribute::className(), 'targetAttribute' => ['child' => 'id']],
        ];
    }

    public function loadData($data)
    {
        $this->_data = $data;
        $this->parent = Arr::get($data, 'parent', null);
        $this->parent_options = array_keys(Arr::get($data, 'parent-options',  []));

        $this->child = Arr::get($data, 'child', null);
        $this->child_options = array_keys(Arr::get($data, 'child-options', []));
    }

    public function attachDependency()
    {
        if (!$this->validate()) {
            dd($this, $this->getErrors(), $this->_data);
            return false;
        }

        // add service_composite_attributes

        foreach ($this->parent_options as $parent_option) {
            $parent = $this->addParent($this->parent, $parent_option);

            if (!$parent) {
                continue;
            }

            $this->addChilds($parent, $this->child, $this->child_options);
        }

        return true;
    }

    protected function addParent($serviceAttributeID, $serviceAttributeOptionID)
    {
        /** @var ServiceCompositeAttribute $serviceCompositeAttribute */
        $serviceCompositeAttribute = ServiceCompositeAttribute::find()
            ->where(['service_attribute_id' => $serviceAttributeID])
            ->andWhere(['service_attribute_option_id' => $serviceAttributeOptionID])
            ->one();

        // TODO: Avoid circular dependency

        if (!$serviceCompositeAttribute) {
            // create a new parent
            $serviceCompositeAttributeParent = $this->createCompositeAttributeParent();

            // add parent attributes..
            $this->createCompositeAttribute($serviceCompositeAttributeParent, $serviceAttributeID, $serviceAttributeOptionID);

            return $serviceCompositeAttributeParent;
        }

        // it' has only one attribute participating.
        if ($serviceCompositeAttribute->serviceCompositeAttributeParent && count($serviceCompositeAttribute->serviceCompositeAttributeParent->serviceCompositeAttributes) === 1) {
            return $serviceCompositeAttribute->serviceCompositeAttributeParent;
        }

        // multiple attribute dependency is not yet set.
        return null;
    }

    /**
     * @return ServiceCompositeAttributeParent
     */
    protected function createCompositeAttributeParent()
    {
        $serviceCompositeAttributeParent = new ServiceCompositeAttributeParent();
        $serviceCompositeAttributeParent->service_id = $this->service_id;
        $serviceCompositeAttributeParent->save();
        return $serviceCompositeAttributeParent;
    }

    /**
     * @param $serviceCompositeAttributeParent ServiceCompositeAttributeParent
     * @param $serviceAttributeID integer
     * @param $serviceAttributeOptionID integer
     */
    protected function createCompositeAttribute($serviceCompositeAttributeParent, $serviceAttributeID, $serviceAttributeOptionID)
    {
        $serviceCompositeAttribute = new ServiceCompositeAttribute();
        $serviceCompositeAttribute->service_attribute_id = $serviceAttributeID;
        $serviceCompositeAttribute->service_attribute_option_id = $serviceAttributeOptionID;
        $serviceCompositeAttribute->service_composite_attribute_parent_id = $serviceCompositeAttributeParent->id;
        $serviceCompositeAttribute->save();
    }

    /**
     * @param $serviceCompositeAttributeParent ServiceCompositeAttributeParent
     * @param $serviceAttributeID integer
     * @param $serviceAttributeOptions array
     */
    protected function addChilds($serviceCompositeAttributeParent, $serviceAttributeID, $serviceAttributeOptions)
    {
        foreach ($serviceAttributeOptions as $serviceAttributeOption) {


            $child = ServiceCompositeAttributeChild::find()
                ->where(['service_attribute_id' => $serviceAttributeID])
                ->andWhere(['service_attribute_option_id' => $serviceAttributeOption])
                ->andWhere(['service_composite_attribute_parent_id' => $serviceCompositeAttributeParent->id])
                ->one();

            if ($child) {
                continue;
            }


            $child = new ServiceCompositeAttributeChild();
            $child->service_composite_attribute_parent_id = $serviceCompositeAttributeParent->id;
            $child->service_attribute_id = $serviceAttributeID;
            $child->service_attribute_option_id = $serviceAttributeOption;
            $child->save();

        }
    }
}