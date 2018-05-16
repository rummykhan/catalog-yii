<?php
/**
 * Created by PhpStorm.
 * User: rummykhan
 * Date: 4/22/18
 * Time: 2:00 PM
 */

namespace common\forms;


use common\models\Attribute;
use common\models\FieldType;
use common\models\InputType;
use common\models\PriceType;
use common\models\PricingAttribute;
use common\models\PricingAttributeGroup;
use common\models\Service;
use common\models\ServiceAttribute;
use common\models\ServiceAttributeOption;
use common\models\ServiceAttributeValidation;
use common\models\UserInputType;
use common\models\Validation;
use frontend\helpers\FieldsConfigurationHelper;
use yii\base\Exception;
use yii\base\Model;
use yii\web\NotFoundHttpException;

class AttachOptions extends Model
{
    public $service_id;
    public $service_attribute_id;

    public $min;
    public $max;
    public $service_attribute_options;
    public $attribute_options;
    public $bulk;

    public $icon;

    public function rules()
    {
        return [
            [['service_id', 'service_attribute_id'], 'required'],
            [['service_id', 'service_attribute_id', 'min', 'max'], 'integer'],
            ['service_attribute_options', 'each', 'rule' => ['safe']],
            ['bulk', 'safe'],
            [['attribute_options'], 'each', 'rule' => ['safe']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'bulk' => 'Add Field values in Bulk (comma separated)'
        ];
    }

    public function attach()
    {
        $service = Service::findOne($this->service_id);
        if (!$service) {
            throw new NotFoundHttpException();
        }

        /** @var ServiceAttribute $serviceAttribute */
        $serviceAttribute = $service->getServiceAttributes()->where(['id' => $this->service_attribute_id])->one();

        if (!$serviceAttribute) {
            throw new NotFoundHttpException();
        }

        if (!$serviceAttribute->fieldType) {
            throw new Exception('Field type not set');
        }

        // check if it's a range attribute add all options
        switch ($serviceAttribute->fieldType->name) {
            case FieldType::TYPE_RANGE:
                // if user selected a new range disable the old range.
                $this->deleteBaseRangeOptions($serviceAttribute, $this->min, $this->max);

                // add the new range.
                $this->addBaseRangeOptions($serviceAttribute, $this->min, $this->max);
                break;

            case FieldType::TYPE_LIST:
                $this->addBaseListOptions($serviceAttribute, $this->attribute_options, $this->service_attribute_options);
                break;
        }

        $this->addBulkOptions($serviceAttribute, $this->bulk);

        return $serviceAttribute;
    }

    /**
     * @param $serviceAttribute ServiceAttribute
     * @param $options array
     * @param $moreOptions array
     * @return bool
     */
    protected function addBaseListOptions($serviceAttribute, $options, $moreOptions = [])
    {

        if (empty($options)) {
            $options = [];
        }

        foreach ($options as $id => $option) {
            $serviceAttributeOption = $serviceAttribute
                ->getServiceAttributeOptions()
                ->where(['id' => $id])
                ->one();

            if (empty($option) && $serviceAttributeOption) {
                $serviceAttributeOption->deleted = true;
                $serviceAttributeOption->save();
            }

            $option = trim($option);

            if (empty($option)) {
                continue;
            }

            if (!$serviceAttributeOption) {
                $serviceAttributeOption = new ServiceAttributeOption();
                $serviceAttributeOption->service_attribute_id = $serviceAttribute->id;
            }

            $serviceAttributeOption->name = $option;
            $serviceAttributeOption->save();
        }

        if (empty($moreOptions)) {
            return true;
        }

        foreach ($moreOptions as $moreOption) {

            $moreOption = trim($moreOption);

            if (empty($moreOption)) {
                continue;
            }

            $serviceAttributeOption = $serviceAttribute->getServiceAttributeOptions()
                ->where(['!=', 'deleted', true])
                ->andWhere(['name' => $moreOption])
                ->one();

            if ($serviceAttributeOption) {
                continue;
            }

            $serviceAttributeOption = new ServiceAttributeOption();
            $serviceAttributeOption->name = $moreOption;
            $serviceAttributeOption->service_attribute_id = $serviceAttribute->id;
            $serviceAttributeOption->save();
        }
    }

    /**
     * @param $serviceAttribute ServiceAttribute
     * @param $options
     * @return mixed
     */
    protected function addBulkOptions($serviceAttribute, $options)
    {
        if (empty($options)) {
            return false;
        }

        $options = explode(',', $options);

        if (empty($options)) {
            return false;
        }

        $this->addBaseListOptions($serviceAttribute, $options);
    }

    /**
     * @param $serviceAttribute ServiceAttribute
     * @param $min
     * @param $max
     */
    protected function deleteBaseRangeOptions($serviceAttribute, $min, $max)
    {
        // e.g. if user choosed a new range
        foreach ($serviceAttribute->serviceAttributeOptions as $serviceAttributeOption) {
            if ($serviceAttributeOption->name < $min || $serviceAttributeOption->name > $max) {
                $serviceAttributeOption->deleted = true;
                $serviceAttributeOption->save();
            }
        }
    }

    /**
     * @param $serviceAttribute ServiceAttribute
     * @param $min integer
     * @param $max integer
     */
    protected function addBaseRangeOptions($serviceAttribute, $min, $max)
    {
        for ($i = $min; $i <= $max; $i++) {
            $serviceAttributeOption = $serviceAttribute->getServiceAttributeOptions()
                ->where(['!=', 'deleted', true])
                ->andWhere(['name' => $i])
                ->one();

            if ($serviceAttributeOption) {
                continue;
            }

            $serviceAttributeOption = new ServiceAttributeOption();
            $serviceAttributeOption->name = $i;
            $serviceAttributeOption->service_attribute_id = $serviceAttribute->id;
            $serviceAttributeOption->save();
        }
    }
}