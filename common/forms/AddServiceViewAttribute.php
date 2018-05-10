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
use common\models\ServiceAttributeView;
use common\models\ServiceView;
use common\models\ServiceViewAttribute;
use yii\base\Model;
use yii\web\NotFoundHttpException;

class AddServiceViewAttribute extends Model
{
    public $service_attributes;
    public $service_id;
    public $view_name;
    public $view_id;

    public function rules()
    {
        return [
            ['view_name', 'safe'],
            ['service_attributes', 'safe'],
            ['service_id', 'integer'],
            ['service_id', 'exist', 'targetClass' => Service::className(), 'targetAttribute' => ['service_id' => 'id']],
            ['view_id', 'integer'],
            ['view_id', 'exist', 'targetClass' => ServiceView::className(), 'targetAttribute' => ['view_id' => 'id']]
        ];
    }

    public function addViewGroup()
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

        // if view id is not empty try to find the view for this service
        if (!empty($this->view_id)) {
            $view = $service->getServiceViews()->where(['id' => $this->view_id])->one();
            if (!$view) {
                throw new NotFoundHttpException();
            }
        } else {
            // create a service view
            $view = new ServiceView();
            $view->name = empty($this->view_name) ? uniqid('PG-', true) : $this->view_name;
        }

        $view->service_id = $this->service_id;
        $view->save();

        foreach ($this->service_attributes as $service_attribute) {
            $this->addViewAttribute($service_attribute, $view);
        }

        return true;
    }

    /**
     * @param $service_attribute_id integer
     * @param $service_view ServiceView
     * @return bool
     */
    public function addViewAttribute($service_attribute_id, $service_view)
    {
        $serviceViewAttribute = ServiceViewAttribute::find()
            ->where(['service_attribute_id' => $service_attribute_id])
            ->one();

        if (!$serviceViewAttribute) {
            // if not create one
            $serviceViewAttribute = new ServiceViewAttribute();
            $serviceViewAttribute->service_attribute_id = $service_attribute_id;
        }

        $serviceViewAttribute->service_view_id = $service_view->id;
        return $serviceViewAttribute->save();
    }
}