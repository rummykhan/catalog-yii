<?php
/**
 * Created by PhpStorm.
 * User: rummykhan
 * Date: 4/18/18
 * Time: 4:02 PM
 */

namespace console\controllers;


use common\models\AttributeInputType;
use common\models\AttributeType;
use common\models\Validation;
use common\models\ValidationOption;
use yii\console\Controller;

class AttributeSeederController extends Controller
{
    public function actionSeed()
    {
        $this->seedAttributeType();
        $this->seedAttributeInputType();
        $this->seedValidationOption();
        $this->seedValidation();
    }

    protected function seedAttributeType()
    {
        if (AttributeType::find()->count() > 0) {
            AttributeType::deleteAll();
        }

        $types = ['string', 'integer', 'date', 'file', 'location'];
        foreach ($types as $type) {
            $attributeType = new AttributeType();
            $attributeType->name = $type;
            $attributeType->save();
        }
    }

    protected function seedAttributeInputType()
    {
        if (AttributeInputType::find()->count() > 0) {
            AttributeInputType::deleteAll();
        }

        $types = ['TextBox', 'DatePicker', 'DateRange', 'TextArea', 'File', 'GoogleMap', 'DropDown', 'Checkbox', 'Radio'];
        foreach ($types as $type) {
            $attributeType = new AttributeInputType();
            $attributeType->name = $type;
            $attributeType->save();
        }
    }

    protected function seedValidationOption()
    {
        if (ValidationOption::find()->count() > 0) {
            ValidationOption::deleteAll();
        }

        $options = ['Min', 'Max'];
        foreach ($options as $option) {
            $validationOption = new ValidationOption();
            $validationOption->name = $option;
            $validationOption->save();
        }
    }

    protected function seedValidation()
    {
        if (Validation::find()->count() > 0) {
            Validation::deleteAll();
        }

        $options = ['required', 'single', 'multiple', 'image', 'doc', 'coordinates', 'phone'];
        foreach ($options as $option) {
            $validation = new Validation();
            $validation->type = $option;
            $validation->save();
        }
    }
}