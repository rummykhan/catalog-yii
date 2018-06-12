<?php

namespace common\models;

use common\queries\NotDeletedQuery;
use frontend\helpers\FieldsConfigurationHelper;
use omgdef\multilingual\MultilingualBehavior;
use omgdef\multilingual\MultilingualQuery;
use RummyKhan\Collection\Arr;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\db\Query;
use yii\web\NotFoundHttpException;
use yiidreamteam\upload\ImageUploadBehavior;

/**
 * This is the model class for table "service_attribute".
 *
 * @property int $id
 * @property int $service_id
 * @property string $question
 * @property string $name
 * @property string $name_ar
 * @property string $description
 * @property string $description_ar
 * @property string $mobile_description
 * @property string $mobile_description_ar
 * @property string $icon
 * @property int $input_type_id
 * @property int $user_input_type_id
 * @property string $validationsString
 * @property boolean $deleted
 * @property int $field_type_id
 * @property int $order
 * @property string $priceType
 *
 * @property Service $service
 * @property FieldType $fieldType
 * @property InputType $inputType
 * @property ServiceAttributeOption[] $serviceAttributeOptions
 * @property Validation[] $validations
 * @property PricingAttribute[] $pricingAttributes
 * @property ServiceCompositeAttribute[] $serviceCompositeAttributes
 * @property ServiceCompositeAttributeChild[] $serviceCompositeAttributeChildren
 *
 * @method getImageFileUrl($attribute)
 */
class ServiceAttribute extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'service_attribute';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['service_id', 'order'], 'integer'],
            [['name'], 'required'],
            [['name', 'description', 'mobile_description', 'question'], 'safe'],
            [['deleted'], 'boolean'],
            [['service_id'], 'exist', 'skipOnError' => true, 'targetClass' => Service::className(), 'targetAttribute' => ['service_id' => 'id']],
            [['input_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => InputType::className(), 'targetAttribute' => ['input_type_id' => 'id']],
            [['user_input_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserInputType::className(), 'targetAttribute' => ['user_input_type_id' => 'id']],
            [['field_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => FieldType::className(), 'targetAttribute' => ['field_type_id' => 'id']],
            ['icon', 'file']
        ];
    }

    public function behaviors()
    {
        return [
            'ml' => [
                'class' => MultilingualBehavior::className(),
                'languages' => Yii::$app->params['languages'],
                'defaultLanguage' => 'en',
                'langForeignKey' => 'service_attribute_id',
                'tableName' => "{{%service_attribute_lang}}",
                'attributes' => [
                    'name', 'description', 'mobile_description'
                ]
            ],
            [
                'class' => ImageUploadBehavior::className(),
                'attribute' => 'icon',
                'thumbs' => [
                    'thumb' => ['width' => 400, 'height' => 300],
                ],
                'filePath' => '@webroot/assets/service-attribute/images/[[pk]].[[extension]]',
                'fileUrl' => '/assets/service-attribute/images/[[pk]].[[extension]]',
                'thumbPath' => '@webroot/assets/service-attribute/images/[[profile]]_[[pk]].[[extension]]',
                'thumbUrl' => '/assets/service-attribute/images/[[profile]]_[[pk]].[[extension]]',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'service_id' => 'Service ID',
            'name' => 'Field Name'
        ];
    }

    public static function find()
    {
        return new MultilingualQuery(get_called_class());
    }

    public function getInputType()
    {
        return $this->hasOne(InputType::className(), ['id' => 'input_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getService()
    {
        return $this->hasOne(Service::className(), ['id' => 'service_id'])->multilingual();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceAttributeOptions()
    {
        return $this->hasMany(ServiceAttributeOption::className(), ['service_attribute_id' => 'id'])->multilingual();
    }

    public function getPricingAttributes()
    {
        return $this->hasMany(PricingAttribute::className(), ['service_attribute_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserInputType()
    {
        return $this->hasOne(UserInputType::className(), ['id' => 'user_input_type_id']);
    }

    public function getFieldType()
    {
        return $this->hasOne(FieldType::className(), ['id' => 'field_type_id']);
    }

    /**
     * @return mixed
     */
    public function getOptionsList()
    {
        $query = (new Query())
            ->select(['attribute_option.name', 'service_attribute_option.id'])
            ->from('service_attribute_option')
            ->join('inner join', 'attribute_option', 'service_attribute_option.attribute_option_id=attribute_option.id')
            ->where(['service_attribute_option.service_attribute_id' => $this->id]);

        return collect($query->all())->pluck('name', 'id');
    }

    /**
     * @return ActiveQuery
     */
    public function getValidations()
    {
        return $this->hasMany(Validation::className(), ['id' => 'validation_id'])
            ->viaTable('service_attribute_validation', ['service_attribute_id' => 'id']);
    }

    public function getValidationsString()
    {
        $attributeValidation = $this->getValidations()->asArray()->all();

        return implode(',', collect($attributeValidation)->pluck('type')->toArray());
    }

    public function getMinimum()
    {
        return $this->getServiceAttributeOptions()->where(['deleted' => false])->one();
    }

    public function getMaximum()
    {
        return $this->getServiceAttributeOptions()->where(['deleted' => false])->orderBy(['id' => SORT_DESC])->one();
    }

    public function getPriceType()
    {
        $pricingAttributes = $this->getPricingAttributes()->all();

        if (empty($pricingAttributes)) {
            return null;
        }

        /** @var PricingAttribute $pricingAttribute */
        $pricingAttribute = Arr::first($pricingAttributes);

        if (empty($pricingAttribute)) {
            return null;
        }

        if ($pricingAttribute->priceType) {
            return $pricingAttribute->priceType->type;
        }

        return null;
    }

    /**
     * @return ActiveQuery
     */
    public function getServiceCompositeAttributes()
    {
        return $this->hasMany(ServiceCompositeAttribute::className(), ['service_attribute_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getServiceCompositeAttributeChildren()
    {
        return $this->hasMany(ServiceCompositeAttributeChild::className(), ['service_attribute_id' => 'id']);
    }

    public function getServiceViewAttributes()
    {
        return $this->hasMany(ServiceViewAttribute::className(), ['service_attribute_id' => 'id']);
    }

}
