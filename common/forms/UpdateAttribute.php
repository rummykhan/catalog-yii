<?php
/**
 * Created by PhpStorm.
 * User: rummykhan
 * Date: 5/2/18
 * Time: 11:00 AM
 */

namespace common\forms;


use common\models\FieldType;
use common\models\InputType;
use common\models\PriceType;
use common\models\PricingAttribute;
use common\models\Service;
use common\models\ServiceAttribute;
use common\models\ServiceAttributeOption;
use common\models\ServiceAttributeValidation;
use common\models\UserInputType;
use frontend\helpers\FieldsConfigurationHelper;
use Yii;
use yii\base\Model;
use yii\web\NotFoundHttpException;

class UpdateAttribute extends Model
{
    public $service_id;
    public $attribute_id;

    public $name;
    public $description;
    public $mobile_description;

    public $name_ar;
    public $description_ar;
    public $mobile_description_ar;

    public $price_type_id;
    public $input_type_id;
    public $user_input_type_id;
    public $field_type_id;

    public $validations;
    public $field_type;
    public $icon;

    public function rules()
    {
        return [
            ['service_id', 'exist', 'targetClass' => Service::className(), 'targetAttribute' => ['service_id' => 'id']],
            ['attribute_id', 'exist', 'targetClass' => ServiceAttribute::className(), 'targetAttribute' => ['attribute_id' => 'id']],
            ['price_type_id', 'exist', 'targetClass' => PriceType::className(), 'targetAttribute' => ['price_type_id' => 'id']],
            ['input_type_id', 'exist', 'targetClass' => InputType::className(), 'targetAttribute' => ['input_type_id' => 'id']],
            ['user_input_type_id', 'exist', 'targetClass' => UserInputType::className(), 'targetAttribute' => ['user_input_type_id' => 'id']],
            ['field_type_id', 'exist', 'targetClass' => FieldType::className(), 'targetAttribute' => ['field_type_id' => 'id']],
            [['attribute_options', 'validations', 'attribute_more_options'], 'each', 'rule' => ['safe']],
            [['name', 'description', 'mobile_description', 'name_ar', 'description_ar', 'mobile_description_ar'], 'safe'],
            [['min', 'max'], 'integer'],
            ['field_type', 'required'],
            ['bulk', 'safe'],
            ['icon', 'file']
        ];
    }

    public function attributeLabels()
    {
        return [
            'attribute_name' => 'Field Name',
        ];
    }

    public function isFieldTypeValid()
    {
        $supportedFieldTypes = FieldsConfigurationHelper::getDropDownData();

        return isset($supportedFieldTypes[$this->field_type]);
    }

    public function attributeNameUnique()
    {
        return 0 == ServiceAttribute::find()
                ->where(['name' => $this->name])
                ->andWhere(['!=', 'deleted', true])
                ->andWhere(['!=', 'id', $this->attribute_id])
                ->andWhere(['service_id' => $this->service_id])
                ->count();
    }

    public function update()
    {
        $post = Yii::$app->getRequest()->post();

        //dd($this, $post);
        /** @var Service $service */
        $service = Service::findOne($this->service_id);

        /** @var ServiceAttribute $serviceAttribute */
        $serviceAttribute = $service->getServiceAttributes()
            ->where(['id' => $this->attribute_id])
            ->one();

        if (!$this->isFieldTypeValid()) {
            $this->addError('field_type', 'Field type not supported.');
            return false;
        }

        if (!$this->attributeNameUnique()) {
            $this->addError('attribute_name', 'Attribute name is not unique.');
            return false;
        }

        if (!$serviceAttribute) {
            throw new NotFoundHttpException();
        }

        /** @var FieldType $fieldType */
        $fieldType = FieldType::find()->where(['name' => $this->field_type])->one();

        // english translations
        $serviceAttribute->name = $this->name;
        $serviceAttribute->description = $this->description;
        $serviceAttribute->mobile_description = $this->mobile_description;

        // arabic translations
        $serviceAttribute->name_ar = $this->name_ar;
        $serviceAttribute->description_ar = $this->description_ar;
        $serviceAttribute->mobile_description_ar = $this->mobile_description_ar;

        $serviceAttribute->field_type_id = $fieldType->id;
        $serviceAttribute->user_input_type_id = $this->user_input_type_id;
        $serviceAttribute->input_type_id = $this->input_type_id;
        $serviceAttribute->save();

        $pricingAttribute = $serviceAttribute
            ->getPricingAttributes()
            ->where(['service_attribute_id' => $serviceAttribute->id])
            ->one();

        if (!$pricingAttribute) {
            $pricingAttribute = new PricingAttribute();
        }

        $pricingAttribute->service_attribute_id = $serviceAttribute->id;
        $pricingAttribute->price_type_id = $this->price_type_id;
        $pricingAttribute->save();

        $this->deleteValidations($serviceAttribute);
        $this->addValidations($serviceAttribute, $this->validations);

        return true;
    }

    /**
     * @param $serviceAttribute ServiceAttribute
     * @param $options
     * @return bool
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

        foreach ($options as $option) {

            $option = trim($option);

            if (empty($option)) {
                continue;
            }

            $serviceAttributeOption = $serviceAttribute->getServiceAttributeOptions()
                ->where(['!=', 'deleted', true])
                ->andWhere(['name' => $option])
                ->one();

            if ($serviceAttributeOption) {
                continue;
            }

            $serviceAttributeOption = new ServiceAttributeOption();
            $serviceAttributeOption->name = $option;
            $serviceAttributeOption->service_attribute_id = $serviceAttribute->id;
            $serviceAttributeOption->save();
        }
    }

    /**
     * @param $serviceAttribute ServiceAttribute
     */
    protected function deleteValidations($serviceAttribute)
    {
        foreach ($serviceAttribute->validations as $validation) {

            $serviceAttributeValidation = ServiceAttributeValidation::find()
                ->where(['service_attribute_id' => $serviceAttribute->id])
                ->andWhere(['validation_id' => $validation->id])
                ->one();


            if (!$serviceAttributeValidation) {
                continue;
            }

            $serviceAttributeValidation->delete();
        }
    }

    /**
     * @param $serviceAttribute ServiceAttribute
     * @param $validations array
     * @return mixed
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

        return true;
    }
}