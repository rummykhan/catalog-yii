<?php

namespace common\models;

use omgdef\multilingual\MultilingualBehavior;
use Yii;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Category::className(), ['id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategories()
    {
        return $this->hasMany(Category::className(), ['parent_id' => 'id']);
    }

    public static function toList()
    {
        return collect(static::find()->asArray()->all())->pluck('name', 'id')->toArray();
    }
}
