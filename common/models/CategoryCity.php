<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "service_city".
 *
 * @property int $id
 * @property int $city_id
 * @property int $category_id
 *
 * @property Category $category
 * @property City $city
 */
class CategoryCity extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'category_city';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['city_id', 'category_id'], 'integer'],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['category_id' => 'id']],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => City::className(), 'targetAttribute' => ['city_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'city_id' => 'City ID',
            'category_id' => 'Category ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'service_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(City::className(), ['id' => 'city_id']);
    }
}
