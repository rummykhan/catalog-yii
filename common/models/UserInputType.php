<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "user_input_type".
 *
 * @property int $id
 * @property string $name
 * @property string $created_at
 * @property string $updated_at
 *
 * @property ServiceAttribute[] $serviceAttributes
 */
class UserInputType extends \yii\db\ActiveRecord
{
    const TYPE_SINGLE = 'Single';
    const TYPE_MULTI = 'Multiple';
    const TYPE_TEXT = 'Text';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_input_type';
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
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at'], 'safe'],
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
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceAttributes()
    {
        return $this->hasMany(ServiceAttribute::className(), ['user_input_type_id' => 'id']);
    }

    public static function toList()
    {
        return collect(static::find()->all())->pluck('name', 'id');
    }
}
