<?php

namespace frontend\helpers;


use common\models\FieldType;
use common\models\InputType;
use common\models\PriceType;
use common\models\UserInputType;

class FieldsConfigurationHelper
{


    public static function get()
    {

        $configuration = json_encode(static::getConfiguration());

        return $configuration;
    }

    public static function getDropDownData()
    {
        return [
            FieldType::TYPE_TEXT => 'Text',
            FieldType::TYPE_RANGE => 'Range / Quantity',
            FieldType::TYPE_LIST => 'List',
            FieldType::TYPE_TOGGLE => 'Boolean',
            FieldType::TYPE_FILE => 'File',
            FieldType::TYPE_LOCATION => 'Google Map',
        ];
    }

    public static function getConfiguration()
    {
        return [
            FieldType::TYPE_TEXT => [
                'inputType' => InputType::find()->where(['name' => InputType::TextBox])->asArray()->one(),
                'userInputType' => UserInputType::find()->where(['name' => UserInputType::TYPE_SINGLE])->asArray()->one(),
                'priceType' => PriceType::find()->where(['type' => PriceType::TYPE_NO_IMPACT])->asArray()->one(),
                'rangeHidden' => true,
                'valueHidden' => true,
            ],
            FieldType::TYPE_RANGE => [
                'inputType' => InputType::find()->where(['name' => InputType::Numeric])->asArray()->one(),
                'userInputType' => UserInputType::find()->where(['name' => UserInputType::TYPE_SINGLE])->asArray()->one(),
                'priceType' => PriceType::find()->where(['type' => PriceType::TYPE_INCREMENTAL])->asArray()->one(),
                'rangeHidden' => false,
                'valueHidden' => true,
            ],
            FieldType::TYPE_LIST => [
                'inputType' => InputType::find()->where(['name' => InputType::DropDown])->asArray()->one(),
                'userInputType' => UserInputType::find()->where(['name' => UserInputType::TYPE_SINGLE])->asArray()->one(),
                'priceType' => PriceType::find()->where(['type' => PriceType::TYPE_COMPOSITE])->asArray()->one(),
                'rangeHidden' => true,
                'valueHidden' => false,
            ],
            FieldType::TYPE_TOGGLE => [
                'inputType' => InputType::find()->where(['name' => InputType::Radio])->asArray()->one(),
                'userInputType' => UserInputType::find()->where(['name' => UserInputType::TYPE_SINGLE])->asArray()->one(),
                'priceType' => PriceType::find()->where(['type' => PriceType::TYPE_NO_IMPACT])->asArray()->one(),
                'rangeHidden' => true,
                'valueHidden' => true,
            ],
            FieldType::TYPE_FILE => [
                'inputType' => InputType::find()->where(['name' => InputType::File])->asArray()->one(),
                'userInputType' => UserInputType::find()->where(['name' => UserInputType::TYPE_SINGLE])->asArray()->one(),
                'priceType' => PriceType::find()->where(['type' => PriceType::TYPE_NO_IMPACT])->asArray()->one(),
                'rangeHidden' => true,
                'valueHidden' => true,
            ],
            FieldType::TYPE_LOCATION => [
                'inputType' => InputType::find()->where(['name' => InputType::DropDown])->asArray()->one(),
                'userInputType' => UserInputType::find()->where(['name' => UserInputType::TYPE_SINGLE])->asArray()->one(),
                'priceType' => PriceType::find()->where(['type' => PriceType::TYPE_COMPOSITE])->asArray()->one(),
                'rangeHidden' => true,
                'valueHidden' => true,
            ]
        ];
    }

}