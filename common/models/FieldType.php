<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "field_type".
 *
 * @property int $id
 * @property string $name
 *
 * @property ServiceAttribute[] $serviceAttributes
 */
class FieldType extends \yii\db\ActiveRecord
{
    const TYPE_TEXT = 'text';
    const TYPE_RANGE = 'range';
    const TYPE_LIST = 'list';
    const TYPE_TOGGLE = 'toggle';
    const TYPE_FILE = 'file';
    const TYPE_LOCATION = 'location';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'field_type';
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
    public function getServiceAttributes()
    {
        return $this->hasMany(ServiceAttribute::className(), ['field_type_id' => 'id']);
    }

    public static function toList()
    {
        return collect(static::find()->asArray()->all())->pluck('name', 'id')->toArray();
    }
}
