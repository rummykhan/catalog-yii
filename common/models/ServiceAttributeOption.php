<?php

namespace common\models;

use common\queries\NotDeletedQuery;
use omgdef\multilingual\MultilingualBehavior;
use omgdef\multilingual\MultilingualQuery;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Expression;

/**
 * This is the model class for table "service_attribute_option".
 *
 * @property int $id
 * @property int $service_attribute_id
 * @property string $name
 * @property string $name_ar
 * @property string $description
 * @property string $description_ar
 * @property string $mobile_description
 * @property string $mobile_description_ar
 * @property string $icon
 * @property int $order
 * @property boolean $deleted
 *
 * @property ServiceAttribute $serviceAttribute
 *
 * @property ServiceCompositeAttribute[] $serviceCompositeAttributes
 * @property ServiceCompositeAttributeChild[] $serviceCompositeAttributeChildren
 */
class ServiceAttributeOption extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'service_attribute_option';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['service_attribute_id', 'order'], 'integer'],
            [['name'], 'required'],
            [['name', 'description', 'mobile_description'], 'safe'],
            [['deleted'], 'boolean'],
            [['service_attribute_id'], 'exist', 'skipOnError' => true, 'targetClass' => ServiceAttribute::className(), 'targetAttribute' => ['service_attribute_id' => 'id']],
            ['icon', 'file'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'service_attribute_id' => 'Service Attribute ID',
            'name' => 'Field Value',
        ];
    }

    public function behaviors()
    {
        return [
            'ml' => [
                'class' => MultilingualBehavior::className(),
                'languages' => Yii::$app->params['languages'],
                'defaultLanguage' => 'en',
                'langForeignKey' => 'service_attribute_option_id',
                'tableName' => "{{%service_attribute_option_lang}}",
                'attributes' => [
                    'name', 'description', 'mobile_description'
                ]
            ],
        ];
    }

    public static function find()
    {
        return new MultilingualQuery(get_called_class());
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceAttribute()
    {
        return $this->hasOne(ServiceAttribute::className(), ['id' => 'service_attribute_id'])->multilingual();
    }

    /**
     * @return ActiveQuery
     */
    public function getServiceCompositeAttributes()
    {
        return $this->hasMany(ServiceCompositeAttribute::className(), ['service_attribute_option_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getServiceCompositeAttributeChildren()
    {
        return $this->hasMany(ServiceCompositeAttributeChild::className(), ['service_attribute_option_id' => 'id']);
    }
}
