<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "validation".
 *
 * @property int $id
 * @property string $type
 * @property string $created_at
 * @property string $updated_at
 *
 * @property ServiceAttributeValidation[] $attributeValidations
 */
class Validation extends \yii\db\ActiveRecord
{
    const Required = 'required';
    const Image = 'image';
    const Doc = 'doc';
    const Coordinates = 'coordinates';
    const Phone = 'phone';
    const Integer = 'integer';
    const String = 'string';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'validation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at'], 'safe'],
            [['type'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
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
    public function getAttributeValidations()
    {
        return $this->hasMany(ServiceAttributeValidation::className(), ['validation_id' => 'id']);
    }

    public static function toList()
    {
        return collect(static::find()->all())->pluck('type', 'id');
    }
}
