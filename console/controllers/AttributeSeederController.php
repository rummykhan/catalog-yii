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
use common\models\FieldType;
use common\models\InputType;
use common\models\AttributeType;
use common\models\PriceType;
use common\models\ServiceType;
use common\models\UserInputType;
use common\models\Validation;
use common\models\ValidationOption;
use PHPUnit\Framework\Constraint\Count;
use Symfony\Component\Console\Input\Input;
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
        $this->seedAttributeInputType();
        $this->seedValidationOption();
        $this->seedValidation();
        $this->seedPriceTypes();
        $this->seedUserInputType();
        $this->seedCountries();
        $this->seedCities();
        $this->seedServiceType();
        $this->addFieldTypes();
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

    protected function seedServiceType()
    {
        $this->stdout('Seeding service type', Console::FG_GREEN);
        $this->stdout("\n");
        if (ServiceType::find()->count() > 0) {
            ServiceType::deleteAll();
        }

        $options = [
            ServiceType::TYPE_IN_HOUSE, ServiceType::TYPE_COLLECT_AND_RETURN
        ];
        foreach ($options as $option) {
            $attributeOption = new ServiceType();
            $attributeOption->type = $option;
            $attributeOption->save();
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
}