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
use common\models\PriceType;
use common\models\Validation;
use common\models\ValidationOption;
use yii\console\Application;
use yii\console\Controller;
use yii\helpers\Console;

class AttributeSeederController extends Controller
{
    public function actionReRun()
    {
        /** @var Application $app */
        $app = \Yii::$app;

        $app->runAction('migrate/down');
        $app->runAction('migrate/up');
        $app->runAction('attribute-seeder/seed');
    }


    public function actionSeed()
    {
        $this->stdout("\n");
        $this->stdout('Running seeders start..', Console::FG_GREEN);
        $this->stdout("\n");
        $this->stdout("\n");

        $this->seedAttributeType();
        $this->seedAttributeInputType();
        $this->seedValidationOption();
        $this->seedValidation();
        $this->seedPriceTypes();

        $this->stdout("\n");
        $this->stdout('Running seeders complete..', Console::FG_GREEN);
        $this->stdout("\n");
    }

    protected function seedAttributeType()
    {
        $this->stdout('Seeding attribute type..', Console::FG_GREEN);
        $this->stdout("\n");
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
        $this->stdout('Seeding attribute input type..', Console::FG_GREEN);
        $this->stdout("\n");
        if (AttributeInputType::find()->count() > 0) {
            AttributeInputType::deleteAll();
        }

        $types = ['TextBox', 'Numeric', 'DatePicker', 'DateRange', 'TextArea', 'File', 'GoogleMap', 'DropDown', 'Checkbox', 'Radio'];
        foreach ($types as $type) {
            $attributeType = new AttributeInputType();
            $attributeType->name = $type;
            $attributeType->save();
        }
    }

    protected function seedValidationOption()
    {
        $this->stdout('Seeding validation options..', Console::FG_GREEN);
        $this->stdout("\n");
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
        $this->stdout('Seeding validation..', Console::FG_GREEN);
        $this->stdout("\n");
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

    protected function seedPriceTypes()
    {
        $this->stdout('Seeding Price Types..', Console::FG_GREEN);
        $this->stdout("\n");
        if (PriceType::find()->count() > 0) {
            PriceType::deleteAll();
        }

        $options = ['composite', 'incremental'];
        foreach ($options as $option) {
            $priceType = new PriceType();
            $priceType->type = $option;
            $priceType->save();
        }
    }
}