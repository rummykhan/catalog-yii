<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace common\oldmodels;

use common\oldmodels\BasketGrid;

use omgdef\multilingual\MultilingualBehavior;
use omgdef\multilingual\MultilingualQuery;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yiidreamteam\upload\ImageUploadBehavior;

/**
 * This is the model class for table "service_attribute".
 *
 * @author Tarek K. Ajaj
 * Apr 4, 2016 4:04:58 PM
 *
 * ServiceAttribute.php
 * UTF-8
 *
 * @property integer $id
 * @property integer $service_id
 * @property string $name
 * @property string $type
 * @property string $question
 * @property boolean $is_optional
 * @property string $mobile_icon
 * @property string $mobile_description
 *
 * @property ProvidedServiceAttribute[] $providedServiceAttributes
 * @property Service $service
 * @property ServiceAttributeOption[] $serviceAttributeOptions
 * @property ServiceAttributeOption[] $serviceAttributeOptionsMultilingual
 * @property ServiceRequestAttribute[] $serviceRequestAttributes
 */
class ServiceAttribute extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'service_attribute';
    }

    public static function getDB()
    {
        return Yii::$app->get("old");
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'question'], 'required'],
            [['type'], 'default', 'value' => ServiceAttributeTypeOption::getKey()],
            [['service_id'], 'integer'],
            [['name', 'type', 'question'], 'string', 'max' => 255],
            [['service_id'], 'exist', 'skipOnError' => true, 'targetClass' => Service::className(), 'targetAttribute' => ['service_id' => 'id']],
            [['is_optional'], 'boolean'],
            [['is_optional'], 'default', 'value' => false],
            ['mobile_icon', 'file', 'extensions' => 'jpg,png', 'maxSize' => 512 * 1024],
            [['mobile_description'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'mobile_icon' => [
                'class' => ImageUploadBehavior::className(),
                'attribute' => 'mobile_icon',
                'thumbs' => [
                    'thumb' => ['width' => 128, 'height' => 128],
                ],
                'filePath' => '@uploadRoot/images/services_attr/service_attr_icon_[[pk]].[[extension]]',
                'fileUrl' => '@uploadWeb/images/services_attr/service_attr_icon_[[pk]].[[extension]]',
                'thumbPath' => '@uploadRoot/images/services_attr/[[profile]]_service_attr_icon_[[pk]].[[extension]]',
                'thumbUrl' => '@uploadWeb/images/services_attr/[[profile]]_service_attr_icon_[[pk]].[[extension]]',
            ],
        ];
    }

    /**
     * @inheritdoc
     *
     * @return MultilingualQuery
     */
    public static function find()
    {
        return new MultilingualQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app/services', 'ID'),
            'service_id' => Yii::t('app/services', 'Service'),
            'name' => Yii::t('app/services', 'Name'),
            'type' => Yii::t('app/services', 'Type'),
            'question' => Yii::t('app/services', 'Question'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getProvidedServiceAttributes()
    {
        return $this->hasMany(ProvidedServiceAttribute::className(), ['service_attribute_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getService()
    {
        return $this->hasOne(Service::className(), ['id' => 'service_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getServiceAttributeOptions()
    {
        return $this->hasMany(ServiceAttributeOption::className(), ['service_attribute_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getServiceAttributeOptionsMultilingual()
    {
        return $this->getServiceAttributeOptions()->multilingual();
    }

    /**
     * @return ActiveQuery
     */
    public function getServiceRequestAttributes()
    {
        return $this->hasMany(ServiceRequestAttribute::className(), ['service_attribute_id' => 'id']);
    }

    public function getBasketGrids()
    {
        return $this->hasMany(BasketGrid::className(), ['service_attribute_id' => 'id']);
    }

    /**
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->service->parent->name . ' / ' . $this->service->name . ' / ' . $this->name;
    }

    /**
     * Render the input based on the attribute type
     * @return string
     */
    public function renderInput($form, $model)
    {
        return ServiceAttributeTypeManager::getServiceAttributeType($this->type)->renderInput($form, $model, $this);
    }

    /**
     * Render the input based on the attribute type
     * @return string
     */
    public function getInputConfiguration($model)
    {
        return ServiceAttributeTypeManager::getServiceAttributeType($this->type)->getInputConfiguration($model, $this);
    }

    public function getAttributeName()
    {
        return "attr_" . $this->id;
    }

    public function getRequiredRule()
    {
        return $this->is_optional ? 'safe' : 'required';
    }

    public static function getAttributeDisplayValue($id, $value)
    {
        return ServiceAttributeTypeManager::getServiceAttributeType(static::findOne($id)->type)->getDisplayValue($value);
    }

    public static function getAttributeIdFromName($name)
    {
        if (strpos($name, "attr_") !== false) {
            return substr($name, strlen("attr_"));
        } else {
            return $name;
        }

    }

    public function generateRule()
    {
        return ServiceAttributeTypeManager::getServiceAttributeType($this->type)->generateRule($this);
    }

    public function getTypeText()
    {
        $types = ServiceAttributeTypeManager::getServiceAttributeTypesList();

        return $types[$this->type];
    }

}
