<?php
/**
 * Created by PhpStorm.
 * User: rummykhan
 * Date: 4/18/18
 * Time: 4:02 PM
 */

namespace console\controllers;


use common\models\Attribute;
use common\models\AttributeOption;
use common\models\City;
use common\models\Country;
use common\models\InputType;
use common\models\AttributeType;
use common\models\PriceType;
use common\models\UserInputType;
use common\models\Validation;
use common\models\ValidationOption;
use PHPUnit\Framework\Constraint\Count;
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

        $this->seedAttributeInputType();
        $this->seedValidationOption();
        $this->seedValidation();
        $this->seedPriceTypes();
        $this->seedUserInputType();
        $this->seedAttributeOptions();
        $this->seedAttributes();
        $this->seedCountries();
        $this->seedCities();

        $this->stdout("\n");
        $this->stdout('Running seeders complete..', Console::FG_GREEN);
        $this->stdout("\n");
    }

    protected function seedAttributeInputType()
    {
        $this->stdout('Seeding attribute input type..', Console::FG_GREEN);
        $this->stdout("\n");
        if (InputType::find()->count() > 0) {
            InputType::deleteAll();
        }

        $types = ['TextBox', 'Numeric', 'DatePicker', 'DateRange', 'TextArea', 'File', 'GoogleMap', 'DropDown', 'Checkbox', 'Radio'];
        foreach ($types as $type) {
            $attributeType = new InputType();
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

        $options = ['required', 'image', 'doc', 'coordinates', 'phone', 'integer', 'string'];
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

        $options = [PriceType::TYPE_COMPOSITE, PriceType::TYPE_INCREMENTAL, PriceType::TYPE_NO_IMPACT];
        foreach ($options as $option) {
            $priceType = new PriceType();
            $priceType->type = $option;
            $priceType->save();
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

    protected function seedAttributeOptions()
    {
        $this->stdout('Seeding attribute options', Console::FG_GREEN);
        $this->stdout("\n");
        if (AttributeOption::find()->count() > 0) {
            AttributeOption::deleteAll();
        }

        $options = [
            1, 2, 3, 4, 'Yes', 'No', 'Male', 'Female', 'Any',
            'Broken Screen', 'Broken Cover', 'Mic not working', 'Headphone not working', 'Not charging',
            'Dubai', 'Sharjah', 'Abu Dhabi', 'Ajman', 'Fujairah',
            'Gold', 'Silver', 'Black', 'Rose Gold', 'White',
            'Protector', 'Mic', 'Headphone', 'Charger', '1BHK', '2BHK', 'Studio', 'Villa'
        ];
        foreach ($options as $option) {
            $attributeOption = new AttributeOption();
            $attributeOption->name = $option;
            $attributeOption->save();
        }
    }

    protected function seedAttributes()
    {
        $this->stdout('Seeding attributes', Console::FG_GREEN);
        $this->stdout("\n");
        if (Attribute::find()->count() > 0) {
            Attribute::deleteAll();
        }

        $options = [
            'Hours', 'No of cleaners', 'Gender Preference', 'From', 'To', 'Issues', 'Color',
            'Accessories', 'Type of House', 'Cleaning Material'
        ];
        foreach ($options as $option) {
            $attributeOption = new Attribute();
            $attributeOption->name = $option;
            $attributeOption->save();
        }
    }

    protected function seedCountries()
    {
        $this->stdout('Seeding countries', Console::FG_GREEN);
        $this->stdout("\n");
        if (Country::find()->count() > 0) {
            Country::deleteAll();
        }

        $options = [
            'United Arab Emirates', 'Saudi Arabia'
        ];
        foreach ($options as $option) {
            $attributeOption = new Country();
            $attributeOption->name = $option;
            $attributeOption->save();
        }
    }

    protected function seedCities()
    {
        $this->stdout('Seeding cities', Console::FG_GREEN);
        $this->stdout("\n");
        if (City::find()->count() > 0) {
            City::deleteAll();
        }

        $countries = [
            'United Arab Emirates' => [
                'Dubai', 'Sharjah', 'Ajman', 'Fujairah', 'Abu Dhabi', 'Al ain', 'Ras Al Khaimah'
            ],
            'Saudi Arabia' => [
                'Riyadh', 'Mecca', 'Medina'
            ]
        ];
        foreach ($countries as $country => $cities) {
            $country = Country::find()
                ->where(['name' => $country])
                ->one();

            if (!$country) {
                continue;
            }

            foreach ($cities as $city) {
                $attributeOption = new City();
                $attributeOption->name = $city;
                $attributeOption->country_id = $country->id;
                $attributeOption->save();
            }
        }
    }
}