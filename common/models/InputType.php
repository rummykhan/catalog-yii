<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "attribute_input_type".
 *
 * @property int $id
 * @property string $name
 * @property string $created_at
 * @property string $updated_at
 *
 * @property ServiceAttribute[] $serviceAttributes
 */
class InputType extends \yii\db\ActiveRecord
{
    const TextBox = 'TextBox';
    const Numeric = 'Numeric';
    const DatePicker = 'DatePicker';
    const DateRange = 'DateRange';
    const TextArea = 'TextArea';
    const File = 'File';
    const GoogleMap = 'GoogleMap';
    const DropDown = 'DropDown';
    const Checkbox = 'Checkbox';
    const Radio = 'Radio';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'input_type';
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
    public function getServiceAttributes()
    {
        return $this->hasMany(ServiceAttribute::className(), ['input_type' => 'id']);
    }

    public static function toList()
    {
        return collect(static::find()->all())->pluck('name', 'id');
    }
}
