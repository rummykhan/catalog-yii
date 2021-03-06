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
use yii\web\UploadedFile;

class AttachAttribute extends Model
{
    public $service_id;

    public $attribute_name;
    public $description;
    public $mobile_description;

    public $attribute_name_ar;
    public $description_ar;
    public $mobile_description_ar;

    public $input_type;
    public $user_input_type;
    public $price_type;
    public $field_type;
    public $validations;

    public $icon;

    public function rules()
    {
        return [
            [['service_id', 'input_type', 'user_input_type'], 'required'],
            [['service_id', 'input_type', 'user_input_type', 'field_type'], 'integer'],
            [['price_type'], 'integer'],
            [['attribute_name'], 'required'],

            [['attribute_name', 'description', 'mobile_description'], 'safe'],
            [['attribute_name_ar', 'description_ar', 'mobile_description_ar'], 'safe'],

            ['input_type', 'exist', 'targetClass' => InputType::className(), 'targetAttribute' => ['input_type' => 'id']],
            ['user_input_type', 'exist', 'targetClass' => UserInputType::className(), 'targetAttribute' => ['user_input_type' => 'id']],
            ['price_type', 'exist', 'targetClass' => PriceType::className(), 'targetAttribute' => ['price_type' => 'id']],
            ['validations', 'each', 'rule' => ['exist', 'targetClass' => Validation::className(), 'targetAttribute' => ['validations' => 'id']]],
            ['field_type', 'exist', 'targetClass' => FieldType::className(), 'targetAttribute' => ['field_type' => 'name']],
            [['service_id', 'attribute_name', 'input_type', 'user_input_type', 'price_type', 'field_type'], 'required'],
            ['icon', 'file']
        ];
    }

    public function attributeLabels()
    {
        return [
            'attribute_name' => 'Field name',
            'bulk' => 'Add Field values in Bulk (comma separated)'
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
        $this->icon = UploadedFile::getInstance($this, 'icon');

        $service = Service::findOne($this->service_id);
        if (!$service) {
            throw new NotFoundHttpException();
        }

        $priceType = PriceType::findOne($this->price_type);

        $fieldType = FieldType::find()->where(['name' => $this->field_type])->one();

        if(!$fieldType){
            throw new Exception('Field type not found');
        }

        // if there is an already attribute with this service with same name
        if (!$this->isAttributeNameUnique()) {
            $this->addError('attribute_name', 'Attribute already added to service');
            return false;
        }

        // create service attribute.
        $serviceAttribute = new ServiceAttribute();

        $serviceAttribute->name = $this->attribute_name;
        $serviceAttribute->name_ar = $this->attribute_name_ar;

        $serviceAttribute->description = $this->description;
        $serviceAttribute->description_ar = $this->description_ar;

        $serviceAttribute->mobile_description = $this->mobile_description;
        $serviceAttribute->mobile_description_ar = $this->mobile_description_ar;

        $serviceAttribute->service_id = $service->id;
        $serviceAttribute->user_input_type_id = $this->user_input_type;
        $serviceAttribute->input_type_id = $this->input_type;
        $serviceAttribute->field_type_id = $fieldType->id;

        if ($this->icon) {
            $serviceAttribute->icon = $this->icon;
        }

        $serviceAttribute->save();


        // create pricing attribute
        $pricingAttribute = new PricingAttribute();
        $pricingAttribute->service_attribute_id = $serviceAttribute->id;
        $pricingAttribute->price_type_id = $priceType->id;
        $pricingAttribute->save();

        $this->addValidations($serviceAttribute, $this->validations);

        return $serviceAttribute;
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