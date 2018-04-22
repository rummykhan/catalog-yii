<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "attribute".
 *
 * @property int $id
 * @property string $name
 * @property int $type
 * @property int $input_type
 * @property string $created_at
 * @property string $updated_at
 *
 * @property AttributeInputType $inputType
 * @property AttributeType $type0
 * @property AttributeValidation[] $attributeValidations
 * @property ServiceAttribute[] $serviceAttributes
 */
class Attribute extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'attribute';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'input_type'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['input_type'], 'exist', 'skipOnError' => true, 'targetClass' => AttributeInputType::className(), 'targetAttribute' => ['input_type' => 'id']],
            [['type'], 'exist', 'skipOnError' => true, 'targetClass' => AttributeType::className(), 'targetAttribute' => ['type' => 'id']],
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
            'type' => 'Type',
            'input_type' => 'Input Type',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()')
            ]
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInputType()
    {
        return $this->hasOne(AttributeInputType::className(), ['id' => 'input_type']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType0()
    {
        return $this->hasOne(AttributeType::className(), ['id' => 'type']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAttributeValidations()
    {
        return $this->hasMany(AttributeValidation::className(), ['attribute_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceAttributes()
    {
        return $this->hasMany(ServiceAttribute::className(), ['attribute_id' => 'id']);
    }
}
