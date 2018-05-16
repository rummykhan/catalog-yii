<?php

namespace common\models;

use omgdef\multilingual\MultilingualBehavior;
use omgdef\multilingual\MultilingualQuery;
use Yii;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\db\Query;
use yiidreamteam\upload\ImageUploadBehavior;

/**
 * This is the model class for table "service".
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $image
 * @property string $description
 * @property string $mobile_description
 * @property string $active
 * @property string $order
 * @property int $category_id
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Category $category
 * @property ServiceAttribute[] $serviceAttributes
 * @property PricingAttributeGroup[] $pricingAttributeGroups
 * @property City[] $cities
 * @property PricingAttributeParent[] $pricingAttributeParents
 * @property ServiceView[] $serviceViews
 * @property ServiceCompositeAttributeParent[] $serviceCompositeAttributeParents
 *
 * @method getImageFileUrl($attribute)
 * @method getThumbFileUrl($attribute)
 */
class Service extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'service';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category_id', 'order'], 'integer'],
            [['created_at', 'updated_at', 'description', 'mobile_description'], 'safe'],
            [['name', 'slug'], 'string', 'max' => 255],
            ['image', 'file'],
            ['active', 'boolean'],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['category_id' => 'id']],
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
                'langForeignKey' => 'service_id',
                'tableName' => "{{%service_lang}}",
                'attributes' => [
                    'name', 'description', 'mobile_description'
                ]
            ],
            [
                'class' => ImageUploadBehavior::className(),
                'attribute' => 'image',
                'thumbs' => [
                    'thumb' => ['width' => 400, 'height' => 300],
                ],
                'filePath' => '@webroot/assets/service/images/[[pk]].[[extension]]',
                'fileUrl' => '/assets/service/images/[[pk]].[[extension]]',
                'thumbPath' => '@webroot/assets/service/images/[[profile]]_[[pk]].[[extension]]',
                'thumbUrl' => '/assets/service/images/[[profile]]_[[pk]].[[extension]]',
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
            'category_id' => 'Category ID',
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
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id'])->multilingual();
    }

    /**
     * @return ActiveQuery
     */
    public function getServiceAttributes()
    {
        return $this->hasMany(ServiceAttribute::className(), ['service_id' => 'id'])->multilingual();
    }

    /**
     * @return ActiveQuery
     */
    public function getPricingAttributeGroups()
    {
        return $this->hasMany(PricingAttributeGroup::className(), ['service_id' => 'id']);
    }

    public function getPricingAttributeParents()
    {
        return $this->hasMany(PricingAttributeParent::className(), ['service_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCities()
    {
        return $this->hasMany(City::className(), ['id' => 'city_id'])
            ->viaTable('service_city', ['service_id' => 'id']);
    }

    public function getCitiesList()
    {
        $query = (new Query())
            ->select(['city.id', 'city.name'])
            ->from('city')
            ->join('inner join', 'service_city', 'city.id=service_city.city_id')
            ->join('inner join', 'service', 'service_city.service_id=service.id')
            ->where(['service.id' => $this->id]);

        return collect($query->all())->pluck('name', 'id');
    }

    /**
     * @return array|ServiceAttribute[]|\yii\db\ActiveRecord[]
     */
    public function getServiceAttributesListNotInPriceGroup()
    {
        $query = (new Query())
            ->select(['service_attribute.id', 'service_attribute.name'])
            ->from('service_attribute')
            ->join('inner join', 'pricing_attribute', 'service_attribute.id=pricing_attribute.service_attribute_id')
            ->join('inner join', 'pricing_attribute_group', 'pricing_attribute.pricing_attribute_group_id=pricing_attribute_group.id')
            ->andWhere(['service_attribute.service_id' => $this->id]);

        $attributes = collect($query->all())->pluck('id')->toArray();

        $query = ServiceAttribute::find()
            ->where(['service_attribute.service_id' => $this->id])
            ->andWhere(['NOT IN', 'service_attribute.id', $attributes]);

        return $query->all();
    }

    /**
     * @return array|ServiceAttribute[]|\yii\db\ActiveRecord[]
     */
    public function getServiceAttributesListNotInServiceView()
    {
        $query = (new Query())
            ->select(['service_attribute.id', 'service_attribute.name'])
            ->from('service_attribute')
            ->join('inner join', 'service_view_attribute', 'service_attribute.id=service_view_attribute.service_attribute_id')
            ->where(['service_attribute.service_id' => $this->id]);

        $attributes = collect($query->all())->pluck('id')->toArray();

        $query = ServiceAttribute::find()
            ->where(['service_attribute.service_id' => $this->id])
            ->andWhere(['NOT IN', 'service_attribute.id', $attributes]);

        return $query->all();
    }

    public function getServiceAttributesList()
    {
        return collect($this->getServiceAttributes()->where(['deleted' => false])->asArray()->all())->pluck('name', 'id');
    }

    /**
     * @param $priceType PriceType
     * @return array
     */
    public function getSelectedPricingAttributesArray($priceType)
    {
        $query = (new Query())
            ->select(['service_attribute.id'])
            ->from('pricing_attribute')
            ->join('inner join', 'service_attribute', 'pricing_attribute.service_attribute_id=service_attribute.id')
            ->join('inner join', 'price_type', 'pricing_attribute.price_type_id=price_type.id')
            ->join('inner join', 'attribute', 'service_attribute.attribute_id=attribute.id')
            ->andWhere(['price_type.type' => $priceType->type])
            ->andWhere(['service_attribute.service_id' => $this->id]);


        return collect($query->all())->pluck('id')->toArray();
    }

    /**
     * @param $priceType PriceType
     * @param $priceGroupID integer
     * @return array
     */
    public function getPricingAttributes($priceType, $priceGroupID = null)
    {
        $query = (new Query())
            ->select([
                new Expression('service_attribute.id as service_attribute_id'),
                new Expression('service_attribute.name attribute_name'),
                new Expression('service_attribute_option.id service_attribute_option_id'),
                new Expression('service_attribute_option.name attribute_option_name')
            ])
            ->from('pricing_attribute')
            ->join('inner join', 'service_attribute', 'pricing_attribute.service_attribute_id=service_attribute.id')
            ->join('inner join', 'price_type', 'pricing_attribute.price_type_id=price_type.id')
            ->join('inner join', 'service_attribute_option', 'service_attribute.id=service_attribute_option.service_attribute_id')
            ->andWhere(['price_type.type' => $priceType->type])
            ->andWhere(['service_attribute_option.deleted' => false])
            ->andWhere(['service_attribute.deleted' => false])
            ->andWhere(['service_attribute.service_id' => $this->id]);

        if (!empty($priceGroupID)) {
            $query->join('inner join', 'pricing_attribute_group', 'pricing_attribute_group.id=pricing_attribute.pricing_attribute_group_id')
                ->andWhere(['pricing_attribute_group.id' => $priceGroupID]);
        } else {
            $query->andWhere(['IS', 'pricing_attribute.pricing_attribute_group_id', NULL]);
        }

        return $query->all();
    }

    public function getAllPricingAttributes()
    {
        $query = PricingAttribute::find()
            ->joinWith(['serviceAttribute', 'serviceAttribute.service'])
            ->where(['service.id' => $this->id]);

        return $query->all();
    }

    /**
     * @return array
     */
    public function getPricingAttributesNotInGroup()
    {
        $query = (new Query())
            ->select([
                new Expression('service_attribute.id as service_attribute_id'),
                new Expression('service_attribute.name attribute_name'),
                new Expression('service_attribute_option.id service_attribute_option_id'),
                new Expression('service_attribute_option.name attribute_option_name')
            ])
            ->from('pricing_attribute')
            ->join('inner join', 'service_attribute', 'pricing_attribute.service_attribute_id=service_attribute.id')
            ->join('inner join', 'price_type', 'pricing_attribute.price_type_id=price_type.id')
            ->join('inner join', 'service_attribute_option', 'service_attribute.id=service_attribute_option.service_attribute_id')
            ->andWhere(['IS NOT', 'price_type.type', NULL])
            ->andWhere(['service_attribute_option.deleted' => false])
            ->andWhere(['service_attribute.deleted' => false])
            ->andWhere(['service_attribute.service_id' => $this->id])
            ->andWhere(['IS', 'pricing_attribute.pricing_attribute_group_id', NULL]);

        return $query->all();
    }

    /**
     * @param $cities array
     * @return mixed
     */
    public function attachCities($cities)
    {
        if (empty($cities)) {
            return null;
        }

        foreach ($cities as $city) {

            $attachedCity = $this->getCities()->where(['id' => $city])->one();

            if ($attachedCity) {
                continue;
            }

            $serviceCity = new ServiceCity();
            $serviceCity->service_id = $this->id;
            $serviceCity->city_id = $city;
            $serviceCity->save(true);
        }
    }

    /**
     * @return ActiveQuery
     */
    public function getServiceViews()
    {
        return $this->hasMany(ServiceView::className(), ['service_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getServiceCompositeAttributeParents()
    {
        return $this->hasMany(ServiceCompositeAttributeParent::className(), ['service_id' => 'id']);
    }
}
