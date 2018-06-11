<?php

namespace common\models;

use omgdef\multilingual\MultilingualBehavior;
use omgdef\multilingual\MultilingualQuery;
use RummyKhan\Collection\Arr;
use Yii;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yiidreamteam\upload\ImageUploadBehavior;

/**
 * This is the model class for table "category".
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $image
 * @property string $description
 * @property string $active
 * @property string $order
 * @property int $parent_id
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Category $parent
 * @property Category[] $categories
 * @property City[] $cities
 *
 * @method getThumbFileUrl($attribute)
 * @method getImageFileUrl($attribute)
 *
 */
class Category extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id'], 'integer'],
            [['created_at', 'updated_at', 'description'], 'safe'],
            [['name', 'slug'], 'string', 'max' => 255],
            [['image'], 'file'],
            ['active', 'boolean'],
            ['order', 'integer'],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['parent_id' => 'id']],
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
            ],
            'ml' => [
                'class' => MultilingualBehavior::className(),
                'languages' => Yii::$app->params['languages'],
                'defaultLanguage' => 'en',
                'langForeignKey' => 'category_id',
                'tableName' => "{{%category_lang}}",
                'attributes' => [
                    'name', 'description',
                ]
            ],
            [
                'class' => ImageUploadBehavior::className(),
                'attribute' => 'image',
                'thumbs' => [
                    'thumb' => ['width' => 400, 'height' => 300],
                ],
                'filePath' => '@webroot/assets/category/images/[[pk]].[[extension]]',
                'fileUrl' => '/assets/category/images/[[pk]].[[extension]]',
                'thumbPath' => '@webroot/category/service/images/[[profile]]_[[pk]].[[extension]]',
                'thumbUrl' => '/assets/category/images/[[profile]]_[[pk]].[[extension]]',
            ],
            [
                'class' => SluggableBehavior::className(),
                'attribute' => 'name',
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
            'parent_id' => 'Parent ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public static function find()
    {
        return new MultilingualQuery(get_called_class());
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Category::className(), ['id' => 'parent_id'])->multilingual();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategories()
    {
        return $this->hasMany(Category::className(), ['parent_id' => 'id'])->multilingual();
    }

    public function getServices()
    {
        return $this->hasMany(Service::className(), ['category_id' => 'id'])->multilingual();
    }

    public static function toList()
    {
        return collect(static::find()->asArray()->all())->pluck('name', 'id')->toArray();
    }

    /**
     * @return ActiveQuery
     */
    public function getCities()
    {
        return $this->hasMany(City::className(), ['id' => 'city_id'])
            ->viaTable('category_city', ['category_id' => 'id']);
    }

    public function getSelectedCities()
    {
        return collect($this->getCities()->asArray()->all())->pluck('name', 'id')->toArray();
    }

    public function getSelectedCountry()
    {
        if (count($this->cities) === 0) {
            return null;
        }

        /** @var City $city */
        $city = Arr::first($this->cities);

        return $city->country_id;
    }

    public function getSelectedCountryCities()
    {
        if (count($this->cities) === 0) {
            return null;
        }

        /** @var City $city */
        $city = Arr::first($this->cities);

        return collect($city->country->getCities()->asArray()->all())->pluck('name', 'id')->toArray();
    }

    public function updateCities($cities)
    {
        // remove previous
        // add these..

        CategoryCity::deleteAll(['category_id' => $this->id]);

        if (empty($cities)) {
            return false;
        }

        foreach ($cities as $city) {
            $categoryCity = new CategoryCity();
            $categoryCity->category_id = $this->id;
            $categoryCity->city_id = $city;
            $categoryCity->save();
        }

        return true;

    }
}
