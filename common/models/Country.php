<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "country".
 *
 * @property int $id
 * @property string $name
 *
 * @property City[] $cities
 */
class Country extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'country';
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
    public function getCities()
    {
        return $this->hasMany(City::className(), ['country_id' => 'id']);
    }

    public static function toList()
    {
        return collect(static::find()->asArray()->all())->pluck('name', 'id')->toArray();
    }

    public static function createMap()
    {
        return static::find()->with(['cities'])->asArray()->all();
    }
}
