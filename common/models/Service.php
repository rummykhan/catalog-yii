<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\db\Query;

/**
 * This is the model class for table "service".
 *
 * @property int $id
 * @property string $name
 * @property int $category_id
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Category $category
 * @property Attribute[] $serviceAttributes
 * @property ServiceAttribute[] $serviceLevelAttributes
 * @property PricingAttributeGroup[] $pricingAttributeGroups
 * @property City[] $cities
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
            [['category_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getServiceAttributes()
    {
        return $this->hasMany(Attribute::className(), ['id' => 'attribute_id'])
            ->viaTable('service_attribute', ['service_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getServiceLevelAttributes()
    {
        return $this->hasMany(ServiceAttribute::className(), ['service_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPricingAttributeGroups()
    {
        return $this->hasMany(PricingAttributeGroup::className(), ['service_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCities()
    {
        return $this->hasMany(City::className(), ['id' => 'city_id'])
            ->viaTable('service_city', ['service_id' => 'id']);
    }

    public function getServiceAttributesListNotInPriceGroup()
    {
        $query = (new Query())
            ->select(['service_attribute.id', 'attribute.name'])
            ->from('service_attribute')
            ->join('inner join', 'attribute', 'service_attribute.attribute_id=attribute.id')
            ->join('inner join', 'pricing_attribute', 'service_attribute.id=pricing_attribute.service_attribute_id')
            ->join('inner join', 'pricing_attribute_group', 'pricing_attribute.pricing_attribute_group_id=pricing_attribute_group.id')
            ->andWhere(['service_attribute.service_id' => $this->id]);

        $attributes = collect($query->all())->pluck('id')->toArray();

        $query = (new Query())
            ->select(['service_attribute.id', 'attribute.name'])
            ->from('service_attribute')
            ->join('inner join', 'attribute', 'service_attribute.attribute_id=attribute.id')
            ->andWhere(['service_attribute.service_id' => $this->id])
            ->andWhere(['NOT IN', 'service_attribute.id', $attributes]);

        return collect($query->all())->pluck('name', 'id');
    }

    public function getServiceAttributesList()
    {
        $query = (new Query())
            ->select(['service_attribute.id', 'attribute.name'])
            ->from('service_attribute')
            ->join('inner join', 'attribute', 'service_attribute.attribute_id=attribute.id')
            ->andWhere(['service_attribute.service_id' => $this->id]);

        return collect($query->all())->pluck('name', 'id');
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
     * @return array
     */
    public function getPricingAttributes($priceType)
    {
        $query = (new Query())
            ->select([
                new Expression('service_attribute.id as service_attribute_id'),
                new Expression('attribute.name attribute_name'),
                new Expression('service_attribute_option.id service_attribute_option_id'),
                new Expression('attribute_option.name attribute_option_name')
            ])
            ->from('pricing_attribute')
            ->join('inner join', 'service_attribute', 'pricing_attribute.service_attribute_id=service_attribute.id')
            ->join('inner join', 'price_type', 'pricing_attribute.price_type_id=price_type.id')
            ->join('inner join', 'attribute', 'service_attribute.attribute_id=attribute.id')
            ->join('inner join', 'service_attribute_option', 'service_attribute.id=service_attribute_option.service_attribute_id')
            ->join('inner join', 'attribute_option', 'service_attribute_option.attribute_option_id=attribute_option.id')
            ->andWhere(['price_type.type' => $priceType->type])
            ->andWhere(['service_attribute.service_id' => $this->id]);


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
}
