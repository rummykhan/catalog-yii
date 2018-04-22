<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "attribute_option".
 *
 * @property int $id
 * @property string $name
 * @property string $created_at
 * @property string $updated_at
 *
 * @property ServiceAttributeOption[] $serviceAttributeOptions
 */
class AttributeOption extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'attribute_option';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()')
            ]
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
            'attribute_id' => 'Attribute ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceAttributeOptions()
    {
        return $this->hasMany(ServiceAttributeOption::className(), ['attribute_option_id' => 'id']);
    }

    public static function toList()
    {
        return collect(static::find()->all())->pluck('name', 'id');
    }
}
