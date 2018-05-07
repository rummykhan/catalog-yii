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
    public $service_attribute_options;
    public $validations;
    public $bulk;

    public function rules()
    {
        return [
            [['service_id', 'input_type', 'user_input_type'], 'required'],
            [['service_id', 'input_type', 'user_input_type', 'min', 'max', 'field_type'], 'integer'],
            [['price_type'], 'integer'],
            [['attribute_name'], 'required'],
            [['attribute_name'], 'safe'],
            ['input_type', 'exist', 'targetClass' => InputType::className(), 'targetAttribute' => ['input_type' => 'id']],
            ['user_input_type', 'exist', 'targetClass' => UserInputType::className(), 'targetAttribute' => ['user_input_type' => 'id']],
            ['price_type', 'exist', 'targetClass' => PriceType::className(), 'targetAttribute' => ['price_type' => 'id']],
            ['service_attribute_options', 'each', 'rule' => ['safe']],
            ['validations', 'each', 'rule' => ['exist', 'targetClass' => Validation::className(), 'targetAttribute' => ['validations' => 'id']]],
            ['field_type', 'exist', 'targetClass' => FieldType::className(), 'targetAttribute' => ['field_type' => 'name']],
            [['service_id', 'attribute_name', 'input_type', 'user_input_type', 'price_type', 'field_type'], 'required'],
            ['bulk', 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'attribute_name' => 'Field name',
            'bulk' => 'Add Bulk values (comma separated)'
        ];
    }

    public function isAttributeNameUnique()
    {
        return 0 == ServiceAttribute::find()
                ->where(['name' => $this->attribute_name])
                ->andWhere(['service_id' => $this->service_id])
                ->andWhere(['deleted' => false])
                ->count();
    }

    public function attach()
    {
        $service = Service::findOne($this->service_id);
        if (!$service) {
            throw new NotFoundHttpException();
        }

        $priceType = PriceType::findOne($this->price_type);

        $fieldType = FieldType::find()->where(['name' => $this->field_type])->one();

        // if there is an already attribute with this service with same name
        if (!$this->isAttributeNameUnique()) {
            $this->addError('attribute_name', 'Attribute already added to service');
            return false;
        }

        // create service attribute.
        $serviceAttribute = new ServiceAttribute();
        $serviceAttribute->name = $this->attribute_name;
        $serviceAttribute->service_id = $service->id;
        $serviceAttribute->user_input_type_id = $this->user_input_type;
        $serviceAttribute->input_type_id = $this->input_type;
        $serviceAttribute->field_type_id = $fieldType->id;
        $serviceAttribute->save();


        // create pricing attribute
        $pricingAttribute = new PricingAttribute();
        $pricingAttribute->service_attribute_id = $serviceAttribute->id;
        $pricingAttribute->price_type_id = $priceType->id;
        $pricingAttribute->save();

        // check if it's a range attribute add all options
        switch ($fieldType->name) {
            case FieldType::TYPE_RANGE:
                $this->addRangeOptions($serviceAttribute, $this->min, $this->max);
                break;

            case FieldType::TYPE_LIST:
                $this->addListOptions($serviceAttribute, $this->service_attribute_options);
                break;
        }

        $this->addValidations($serviceAttribute, $this->validations);

        $this->addBulkOptions($serviceAttribute, $this->bulk);

        return $serviceAttribute;
    }

    /**
     * @param $serviceAttribute ServiceAttribute
     * @param $min
     * @param $max
     */
    protected function addRangeOptions($serviceAttribute, $min, $max)
    {
        for ($i = $min; $i <= $max; $i++) {

            $option = ServiceAttributeOption::find()
                ->where(['service_attribute_id' => $serviceAttribute->id])
                ->andWhere(['name' => $i])
                ->andWhere(['deleted' => false])
                ->one();

            if ($option) {
                continue;
            }

            $option = new ServiceAttributeOption();
            $option->name = $i;
            $option->service_attribute_id = $serviceAttribute->id;
            $option->save();
        }
    }

    /**
     * @param $serviceAttribute ServiceAttribute
     * @param $options array
     */
    protected function addListOptions($serviceAttribute, $options)
    {
        if (empty($options) || !is_array($options)) {
            return false;
        }


        foreach ($options as $option) {

            $option = trim($option);

            if (empty($option)) {
                continue;
            }

            $attributeOption = $serviceAttribute
                ->getServiceAttributeOptions()
                ->where(['name' => $option])
                ->andWhere(['deleted' => false])
                ->one();

            if ($attributeOption) {
                continue;
            }

            $attributeOption = new ServiceAttributeOption();
            $attributeOption->name = $option;
            $attributeOption->service_attribute_id = $serviceAttribute->id;
            $attributeOption->save();
        }
    }

    /**
     * @param $serviceAttribute ServiceAttribute
     * @param $validations array
     */
    protected function addValidations($serviceAttribute, $validations)
    {
        if (empty($validations)) {
            return true;
        }

        foreach ($validations as $validation) {
            $attributeValidation = $serviceAttribute->getValidations()
                ->where(['id' => $validation])
                ->one();

            if ($attributeValidation) {
                continue;
            }

            $attributeValidation = new ServiceAttributeValidation();
            $attributeValidation->service_attribute_id = $serviceAttribute->id;
            $attributeValidation->validation_id = $validation;
            $attributeValidation->save();
        }
    }

    /**
     * @param $serviceAttribute ServiceAttribute
     * @param $options
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

        $this->addListOptions($serviceAttribute, $options);
    }
}