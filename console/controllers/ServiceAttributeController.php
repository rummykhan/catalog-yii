<?php
/**
 * Created by PhpStorm.
 * User: rummykhan
 * Date: 6/12/18
 * Time: 2:01 AM
 */

namespace console\controllers;


use common\models\FieldType;
use common\models\InputType;
use common\models\PriceType;
use common\models\PricingAttribute;
use common\models\PricingAttributeGroup;
use common\models\ServiceAttributeOption;
use common\models\ServiceAttributeValidation;
use common\models\UserInputType;
use common\models\Validation;
use common\models\ValidationOption;
use common\oldmodels\BasketGrid;
use common\oldmodels\ServiceAttribute;
use yii\console\Controller;
use yii\helpers\Console;

class ServiceAttributeController extends Controller
{
    public function actionMigrate()
    {
        \Yii::$app->getDb()->createCommand('DELETE FROM pricing_attribute_group WHERE id > :id', [':id' => 0])->execute();
        \Yii::$app->getDb()->createCommand('DELETE FROM pricing_attribute WHERE id > :id', [':id' => 0])->execute();
        \Yii::$app->getDb()->createCommand('DELETE FROM service_attribute_option WHERE id > :id', [':id' => 0])->execute();
        \Yii::$app->getDb()->createCommand('DELETE FROM service_attribute WHERE id > :id', [':id' => 0])->execute();

        $this->seedAttributeInputType();
        $this->seedValidationOption();
        $this->seedValidation();
        $this->seedPriceTypes();
        $this->seedUserInputType();
        $this->addFieldTypes();


        $oldServiceAttributes = ServiceAttribute::find();

        /** @var ServiceAttribute $oldServiceAttribute */
        foreach ($oldServiceAttributes->each(20) as $oldServiceAttribute) {

            switch ($oldServiceAttribute->type) {
                case 10: // single selection
                    $this->addSingleAttribute($oldServiceAttribute);
                    break;

                case 30: // free text
                    $this->addTextAttribute($oldServiceAttribute);
                    break;

                case 40: // multiple selection
                    $this->addMultiAttribute($oldServiceAttribute);
                    break;

                case 50: // basket
                    $this->addBasketAttribute($oldServiceAttribute);
                    break;
            }


        }
    }

    /**
     * @param $oldServiceAttribute ServiceAttribute
     */
    private function addSingleAttribute($oldServiceAttribute)
    {
        $attribute = [
            'name' => $oldServiceAttribute->name,
            'question' => $oldServiceAttribute->question,
            'description' => $oldServiceAttribute->mobile_description,
            'mobile_description' => $oldServiceAttribute->mobile_description,
            'is_required' => $oldServiceAttribute->is_optional == 1,
            'service_id' => $oldServiceAttribute->service_id,
            'input_type' => InputType::DropDown,
            'user_input_type' => UserInputType::TYPE_SINGLE, // user will choose single
            'field_type' => FieldType::TYPE_LIST,
            'price_type' => PriceType::TYPE_INDEPENDENT,
        ];

        $options = $oldServiceAttribute->getServiceAttributeOptions()->asArray()->all();
        /**
         * {"id" => "5274"
         * "service_attribute_id" => "105"
         * "value" => "Touch Issue"
         * "option_order" => "99"}
         */
        $this->createAttribute($attribute, $options);
    }

    /**
     * @param $oldServiceAttribute ServiceAttribute
     */
    private function addMultiAttribute($oldServiceAttribute)
    {
        $attribute = [
            'name' => $oldServiceAttribute->name,
            'question' => $oldServiceAttribute->question,
            'description' => $oldServiceAttribute->mobile_description,
            'mobile_description' => $oldServiceAttribute->mobile_description,
            'is_required' => $oldServiceAttribute->is_optional == 1,
            'service_id' => $oldServiceAttribute->service_id,
            'input_type' => InputType::DropDown,
            'user_input_type' => UserInputType::TYPE_MULTI,
            'field_type' => FieldType::TYPE_LIST,
            'price_type' => PriceType::TYPE_INDEPENDENT,
        ];

        $options = $oldServiceAttribute->getServiceAttributeOptions()->asArray()->all();
        /**
         * {"id" => "5944"
         * "service_attribute_id" => "1"
         * "value" => "Not Vibrating"
         * "option_order" => "99"}
         */
        $this->createAttribute($attribute, $options);
    }

    /**
     * @param $oldServiceAttribute ServiceAttribute
     */
    private function addTextAttribute($oldServiceAttribute)
    {
        $attribute = [
            'name' => $oldServiceAttribute->name,
            'question' => $oldServiceAttribute->question,
            'description' => $oldServiceAttribute->mobile_description,
            'mobile_description' => $oldServiceAttribute->mobile_description,
            'is_required' => $oldServiceAttribute->is_optional == 1,
            'service_id' => $oldServiceAttribute->service_id,
            'input_type' => InputType::TextArea,
            'user_input_type' => UserInputType::TYPE_TEXT,
            'field_type' => FieldType::TYPE_TEXT,
            'price_type' => PriceType::TYPE_NO_IMPACT,
        ];

        $this->createAttribute($attribute, []);
    }

    /**
     * @param $oldServiceAttribute ServiceAttribute
     */
    private function addBasketAttribute($oldServiceAttribute)
    {
        $pricingAttributeGroup = $this->createPricingAttributeGroup($oldServiceAttribute);

        /** @var BasketGrid $basketGrid */
        foreach ($oldServiceAttribute->getBasketGrids()->all() as $basketGrid) {

            $attribute = [
                'name' => $basketGrid->basket_items_label,
                'type' => 'items',
                'grid_id' => $basketGrid->id,
                'question' => null,
                'description' => null,
                'mobile_description' => null,
                'is_required' => true,
                'service_id' => $oldServiceAttribute->service_id,
                'input_type' => InputType::DropDown,
                'user_input_type' => UserInputType::TYPE_SINGLE,
                'field_type' => FieldType::TYPE_LIST,
                'price_type' => PriceType::TYPE_COMPOSITE,
            ];

            $options = $basketGrid->getBasketItems()->asArray()->all();
            /**
             * "id" => "869"
             * "basket_grid_id" => "4"
             * "value" => "Winter Jackets"
             * "option_order" => "99"
             * "image" => null
             * "mobile_icon" => "Winter-Jacket.png"
             * "mobile_description" => ""
             */
            $this->createAttribute($attribute, $options, $pricingAttributeGroup);


            $attribute = [
                'name' => $basketGrid->basket_services_label,
                'type' => 'service',
                'grid_id' => $basketGrid->id,
                'question' => null,
                'description' => null,
                'mobile_description' => null,
                'is_required' => true,
                'service_id' => $oldServiceAttribute->service_id,
                'input_type' => InputType::DropDown,
                'user_input_type' => UserInputType::TYPE_SINGLE,
                'field_type' => FieldType::TYPE_LIST,
                'price_type' => PriceType::TYPE_COMPOSITE,
            ];

            $options = $basketGrid->getBasketServices()->asArray()->all();
            /**
             * "id" => "31"
             * "basket_grid_id" => "4"
             * "value" => "Dry Cleaning"
             * "option_order" => "1"
             * "mobile_icon" => "Dry Cleaning.png"
             * "mobile_description" => "Cleaning + pressing"
             */
            $this->createAttribute($attribute, $options, $pricingAttributeGroup);

            $attribute = [
                'name' => $basketGrid->quantity_unit,
                'type' => 'quantity',
                'grid_id' => $basketGrid->id,
                'question' => null,
                'description' => null,
                'mobile_description' => null,
                'is_required' => true,
                'service_id' => $oldServiceAttribute->service_id,
                'input_type' => InputType::Numeric,
                'user_input_type' => UserInputType::TYPE_SINGLE,
                'field_type' => FieldType::TYPE_RANGE,
                'price_type' => PriceType::TYPE_INCREMENTAL,
            ];
            $start = 0;
            $end = 9;

            if (!empty($basketGrid->min_qty)) {
                $start = $basketGrid->min_qty;
            }

            if (!empty($basketGrid->max_qty)) {
                $end = $basketGrid->max_qty;
            }

            $options = array_map(function ($value) {
                return [
                    'value' => $value,
                ];
            }, range($start, $end));

            /**
             * {"value" => 7}
             */
            $this->createAttribute($attribute, $options, $pricingAttributeGroup);
        }
    }

    /**
     * @param $oldServiceAttribute ServiceAttribute
     * @return PricingAttributeGroup
     */
    private function createPricingAttributeGroup($oldServiceAttribute)
    {
        $pricingAttributeGroup = new PricingAttributeGroup();
        $pricingAttributeGroup->name = $oldServiceAttribute->name;
        $pricingAttributeGroup->service_id = $oldServiceAttribute->service_id;
        $pricingAttributeGroup->save();

        return $pricingAttributeGroup;
    }

    /**
     * @param $attributeValues array
     * @param $options array
     * @param $group PricingAttributeGroup
     * @return mixed
     */
    private function createAttribute($attributeValues, $options, $group = null)
    {
        if (empty($attributeValues['question']) && empty($attributeValues['name'])) {
            return false;
        }

        $inputType = InputType::find()->where(['name' => $attributeValues['input_type']])->one();
        $userInputType = UserInputType::find()->where(['name' => $attributeValues['user_input_type']])->one();
        $fieldType = FieldType::find()->where(['name' => $attributeValues['field_type']])->one();
        $priceType = PriceType::find()->where(['type' => $attributeValues['price_type']])->one();
        $requireValidation = Validation::find()->where(['type' => Validation::Required])->one();

        // Save Service Attribute
        $serviceAttribute = new \common\models\ServiceAttribute();
        $serviceAttribute->service_id = $attributeValues['service_id'];
        $serviceAttribute->name = $attributeValues['name'];
        $serviceAttribute->question = $attributeValues['question'];
        $serviceAttribute->description = $attributeValues['description'];
        $serviceAttribute->mobile_description = $attributeValues['mobile_description'];
        $serviceAttribute->input_type_id = $inputType ? $inputType->id : null;
        $serviceAttribute->user_input_type_id = $userInputType ? $userInputType->id : null;
        $serviceAttribute->field_type_id = $fieldType ? $fieldType->id : null;
        $serviceAttribute->save();


        // Save Pricing Attribute
        $pricingAttribute = new PricingAttribute();
        $pricingAttribute->service_id = $attributeValues['service_id'];
        $pricingAttribute->service_attribute_id = $serviceAttribute->id;
        $pricingAttribute->price_type_id = $priceType ? $priceType->id : null;

        if (!empty($group)) {
            $pricingAttribute->pricing_attribute_group_id = $group->id;
        }

        $pricingAttribute->save();

        // Save Validations
        if ($attributeValues['is_required'] && $requireValidation) {
            $validation = new ServiceAttributeValidation();
            $validation->service_attribute_id = $serviceAttribute->id;
            $validation->validation_id = $requireValidation->id;
            $validation->save();
        }

        // save options
        foreach ($options as $option) {
            $serviceAttributeOption = new ServiceAttributeOption();
            $serviceAttributeOption->service_attribute_id = $serviceAttribute->id;
            $serviceAttributeOption->name = $option['value'];
            $serviceAttributeOption->save();
        }


        $this->stdout($attributeValues['service_id'] . " - " . $attributeValues['name'] . ' - ' . $attributeValues['question']);
        $this->stdout("\r\n");
    }


    protected function seedAttributeInputType()
    {
        $this->stdout('Seeding attribute input type..', Console::FG_GREEN);
        $this->stdout("\n");
        if (InputType::find()->count() > 0) {
            InputType::deleteAll();
        }

        $types = [
            InputType::TextBox,
            InputType::Numeric,
            InputType::DatePicker,
            InputType::DateRange,
            InputType::TextArea,
            InputType::File,
            InputType::GoogleMap,
            InputType::DropDown,
            InputType::Checkbox,
            InputType::Radio
        ];
        foreach ($types as $type) {
            $attributeType = new InputType();
            $attributeType->name = $type;
            $attributeType->save();
        }
    }

    protected function seedUserInputType()
    {
        $this->stdout('Seeding user input type', Console::FG_GREEN);
        $this->stdout("\n");
        if (UserInputType::find()->count() > 0) {
            UserInputType::deleteAll();
        }

        $options = [UserInputType::TYPE_SINGLE, UserInputType::TYPE_MULTI, UserInputType::TYPE_TEXT];
        foreach ($options as $option) {
            $priceType = new UserInputType();
            $priceType->name = $option;
            $priceType->save();
        }
    }

    protected function addFieldTypes()
    {
        $this->stdout('Seeding field type', Console::FG_GREEN);
        $this->stdout("\n");
        if (FieldType::find()->count() > 0) {
            FieldType::deleteAll();
        }

        $options = [
            FieldType::TYPE_LIST,
            FieldType::TYPE_RANGE,
            FieldType::TYPE_LOCATION,
            FieldType::TYPE_FILE,
            FieldType::TYPE_TOGGLE,
            FieldType::TYPE_TEXT
        ];
        foreach ($options as $option) {
            $attributeOption = new FieldType();
            $attributeOption->name = $option;
            $attributeOption->save();
        }
    }

    protected function seedPriceTypes()
    {
        $this->stdout('Seeding Price Types..', Console::FG_GREEN);
        $this->stdout("\n");
        if (PriceType::find()->count() > 0) {
            PriceType::deleteAll();
        }

        $options = [
            PriceType::TYPE_COMPOSITE,
            PriceType::TYPE_INCREMENTAL,
            PriceType::TYPE_NO_IMPACT,
            PriceType::TYPE_INDEPENDENT
        ];
        foreach ($options as $option) {
            $priceType = new PriceType();
            $priceType->type = $option;
            $priceType->save();
        }
    }

    protected function seedValidationOption()
    {
        $this->stdout('Seeding validation options..', Console::FG_GREEN);
        $this->stdout("\n");
        if (ValidationOption::find()->count() > 0) {
            ValidationOption::deleteAll();
        }

        $options = [ValidationOption::Min, ValidationOption::Max];
        foreach ($options as $option) {
            $validationOption = new ValidationOption();
            $validationOption->name = $option;
            $validationOption->save();
        }
    }

    protected function seedValidation()
    {
        $this->stdout('Seeding validation..', Console::FG_GREEN);
        $this->stdout("\n");
        if (Validation::find()->count() > 0) {
            Validation::deleteAll();
        }

        $options = [Validation::Required, Validation::Image, Validation::Doc, Validation::Coordinates, Validation::Phone, Validation::Integer, Validation::String];
        foreach ($options as $option) {
            $validation = new Validation();
            $validation->type = $option;
            $validation->save();
        }
    }


}