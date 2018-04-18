<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "attribute_validation_option".
 *
 * @property int $id
 * @property int $attribute_validation_id
 * @property int $validation_option_id
 * @property string $value
 *
 * @property ValidationOption $attributeValidation
 * @property ValidationOption $validationOption
 */
class AttributeValidationOption extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'attribute_validation_option';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['attribute_validation_id', 'validation_option_id'], 'integer'],
            [['value'], 'string', 'max' => 255],
            [['attribute_validation_id'], 'exist', 'skipOnError' => true, 'targetClass' => ValidationOption::className(), 'targetAttribute' => ['attribute_validation_id' => 'id']],
            [['validation_option_id'], 'exist', 'skipOnError' => true, 'targetClass' => ValidationOption::className(), 'targetAttribute' => ['validation_option_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'attribute_validation_id' => 'Attribute Validation ID',
            'validation_option_id' => 'Validation Option ID',
            'value' => 'Value',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAttributeValidation()
    {
        return $this->hasOne(ValidationOption::className(), ['id' => 'attribute_validation_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getValidationOption()
    {
        return $this->hasOne(ValidationOption::className(), ['id' => 'validation_option_id']);
    }
}
