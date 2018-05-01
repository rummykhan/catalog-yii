<?php

namespace frontend\helpers;


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

    public static function getConfiguration()
    {
        return [
            'text' => [
                'inputType' => InputType::find()->where(['name' => InputType::TextBox])->asArray()->one(),
                'userInputType' => UserInputType::find()->where(['name' => UserInputType::TYPE_SINGLE])->asArray()->one(),
                'priceType' => PriceType::find()->where(['type' => PriceType::TYPE_NO_IMPACT])->asArray()->one(),
                'rangeHidden' => true,
            ],
            'range' => [
                'inputType' => InputType::find()->where(['name' => InputType::Numeric])->asArray()->one(),
                'userInputType' => UserInputType::find()->where(['name' => UserInputType::TYPE_SINGLE])->asArray()->one(),
                'priceType' => PriceType::find()->where(['type' => PriceType::TYPE_INCREMENTAL])->asArray()->one(),
                'rangeHidden' => false,
            ],
            'list' => [
                'inputType' => InputType::find()->where(['name' => InputType::DropDown])->asArray()->one(),
                'userInputType' => UserInputType::find()->where(['name' => UserInputType::TYPE_MULTI])->asArray()->one(),
                'priceType' => PriceType::find()->where(['type' => PriceType::TYPE_COMPOSITE])->asArray()->one(),
                'rangeHidden' => true,
            ],
            'toggle' => [
                'inputType' => InputType::find()->where(['name' => InputType::Radio])->asArray()->one(),
                'userInputType' => UserInputType::find()->where(['name' => UserInputType::TYPE_SINGLE])->asArray()->one(),
                'priceType' => PriceType::find()->where(['type' => PriceType::TYPE_NO_IMPACT])->asArray()->one(),
                'rangeHidden' => true,
            ],
            'file' => [
                'inputType' => InputType::find()->where(['name' => InputType::File])->asArray()->one(),
                'userInputType' => UserInputType::find()->where(['name' => UserInputType::TYPE_SINGLE])->asArray()->one(),
                'priceType' => PriceType::find()->where(['type' => PriceType::TYPE_NO_IMPACT])->asArray()->one(),
                'rangeHidden' => true,
            ],
            'location' => [
                'inputType' => InputType::find()->where(['name' => InputType::DropDown])->asArray()->one(),
                'userInputType' => UserInputType::find()->where(['name' => UserInputType::TYPE_SINGLE])->asArray()->one(),
                'priceType' => PriceType::find()->where(['type' => PriceType::TYPE_COMPOSITE])->asArray()->one(),
                'rangeHidden' => true,
            ]
        ];
    }

}