<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "attribute_validation".
 *
 * @property int $id
 * @property int $attribute_id
 * @property int $validation_id
 *
 * @property Attribute $attribute0
 * @property Validation $validation
 */
class AttributeValidation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'attribute_validation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['attribute_id', 'validation_id'], 'integer'],
            [['attribute_id'], 'exist', 'skipOnError' => true, 'targetClass' => Attribute::className(), 'targetAttribute' => ['attribute_id' => 'id']],
            [['validation_id'], 'exist', 'skipOnError' => true, 'targetClass' => Validation::className(), 'targetAttribute' => ['validation_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'attribute_id' => 'Attribute ID',
            'validation_id' => 'Validation ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAttribute0()
    {
        return $this->hasOne(Attribute::className(), ['id' => 'attribute_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getValidation()
    {
        return $this->hasOne(Validation::className(), ['id' => 'validation_id']);
    }
}
