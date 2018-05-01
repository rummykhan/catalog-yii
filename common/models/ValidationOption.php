<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "validation_option".
 *
 * @property int $id
 * @property string $name
 *
 * @property AttributeValidationOption[] $attributeValidationOptions
 * @property AttributeValidationOption[] $attributeValidationOptions0
 */
class ValidationOption extends \yii\db\ActiveRecord
{
    const Min = 'Min';
    const Max = 'Max';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'validation_option';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAttributeValidationOptions()
    {
        return $this->hasMany(AttributeValidationOption::className(), ['attribute_validation_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAttributeValidationOptions0()
    {
        return $this->hasMany(AttributeValidationOption::className(), ['validation_option_id' => 'id']);
    }
}
