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
use yii\console\Controller;

class AttributeSeederController extends Controller
{
    public function actionSeed()
    {
        $this->seedAttributeType();
        $this->seedAttributeInputType();
        $this->seedAttributeValidation();
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

    protected function seedAttributeValidation()
    {

    }
}