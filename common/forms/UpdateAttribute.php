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

    public $attribute_options;
    public $attribute_more_options;
    public $attribute_validations;

    public $min;
    public $max;

    public $field_type;

    public $bulk;

    public function rules()
    {
        return [
            ['service_id', 'exist', 'targetClass' => Service::className(), 'targetAttribute' => ['service_id' => 'id']],
            ['attribute_id', 'exist', 'targetClass' => ServiceAttribute::className(), 'targetAttribute' => ['attribute_id' => 'id']],
            ['price_type_id', 'exist', 'targetClass' => PriceType::className(), 'targetAttribute' => ['price_type_id' => 'id']],
            ['input_type_id', 'exist', 'targetClass' => InputType::className(), 'targetAttribute' => ['input_type_id' => 'id']],
            ['user_input_type_id', 'exist', 'targetClass' => UserInputType::className(), 'targetAttribute' => ['user_input_type_id' => 'id']],
            ['field_type_id', 'exist', 'targetClass' => FieldType::className(), 'targetAttribute' => ['field_type_id' => 'id']],
            [['attribute_options', 'attribute_validations', 'attribute_more_options'], 'each', 'rule' => ['safe']],
            [['name', 'description', 'mobile_description', 'name_ar', 'description_ar', 'mobile_description_ar'], 'safe'],
            [['min', 'max'], 'integer'],
            ['field_type', 'required'],
            ['bulk', 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'attribute_name' => 'Field Name',
            'bulk' => 'Add Bulk values (comma separated)'
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

        $baseFieldType = FieldType::findOne($serviceAttribute->field_type_id);

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

        switch ($fieldType->name) {
            case FieldType::TYPE_LIST:
                $this->addListOptions($serviceAttribute, $baseFieldType, $this->attribute_options, $this->attribute_more_options);
                break;

            case FieldType::TYPE_RANGE:
                $this->addRangeOptions($serviceAttribute, $baseFieldType, $this->min, $this->max);
                break;
        }

        $this->addBulkOptions($serviceAttribute, $this->bulk);

        return true;
    }

    /**
     * @param $serviceAttribute ServiceAttribute
     * @param $baseFieldType FieldType
     * @param $options array
     * @param $moreOptions array
     */
    protected function addListOptions($serviceAttribute, $baseFieldType, $options, $moreOptions)
    {
        switch ($baseFieldType->name) {
            case FieldType::TYPE_LIST:
                // add list options just check if it's already existing, don't need to add
                $this->addBaseListOptions($serviceAttribute, $options, $moreOptions);
                break;

            case FieldType::TYPE_RANGE:
                $this->deleteOptions($serviceAttribute);
                // delete old options
                // add new list options
                $this->addBaseListOptions($serviceAttribute, $options, $moreOptions);
                break;
        }
    }

    /**
     * @param $serviceAttribute ServiceAttribute
     * @param $baseFieldType FieldType
     * @param $min integer
     * @param $max integer
     */
    protected function addRangeOptions($serviceAttribute, $baseFieldType, $min, $max)
    {
        switch ($baseFieldType->name) {
            case FieldType::TYPE_LIST:
                // delete old options
                $this->deleteOptions($serviceAttribute);

                // add range options
                $this->addBaseRangeOptions($serviceAttribute, $min, $max);
                break;

            case FieldType::TYPE_RANGE:
                // add range options just check if it's already existing don't need to add.

                // if user selected a new range disable the old range.
                $this->deleteBaseRangeOptions($serviceAttribute, $min, $max);

                // add the new range.
                $this->addBaseRangeOptions($serviceAttribute, $min, $max);
                break;
        }
    }

    /**
     * @param $serviceAttribute ServiceAttribute
     * @param $options array
     * @param $moreOptions array
     */
    protected function addBaseListOptions($serviceAttribute, $options, $moreOptions)
    {

        if (empty($options)) {
            $options = [];
        }
        foreach ($options as $id => $option) {
            $serviceAttributeOption = $serviceAttribute->getServiceAttributeOptions()
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
     */
    protected function deleteOptions($serviceAttribute)
    {
        foreach ($serviceAttribute->serviceAttributeOptions as $option) {
            $option->deleted = true;
            $option->save();
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
}