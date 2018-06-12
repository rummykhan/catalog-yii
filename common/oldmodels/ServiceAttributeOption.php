<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace common\oldmodels;

use common\models\providedservices\ProvidedServiceAttributeOption;
use omgdef\multilingual\MultilingualBehavior;
use omgdef\multilingual\MultilingualQuery;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "service_attribute_option".
 *
 * @author Tarek K. Ajaj
 * Apr 4, 2016 4:07:24 PM
 * 
 * ServiceAttributeOption.php
 * UTF-8
 * 
 * @property integer $id
 * @property integer $service_attribute_id
 * @property string $value
 * @property integer $option_order
 *
 * @property ProvidedServiceAttributeOption[] $providedServiceAttributeOptions
 * @property ServiceAttribute $serviceAttribute
 */
class ServiceAttributeOption extends ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'service_attribute_option';
    }

    public static function getDB()
    {
        return Yii::$app->get("old");
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [[/*'service_attribute_id',*/ 'value'], 'required'],
            [['service_attribute_id', 'option_order'], 'integer'],
            [['value'], 'string', 'max' => 255],
            [['service_attribute_id'], 'exist', 'skipOnError' => true, 'targetClass' => ServiceAttribute::className(), 'targetAttribute' => ['service_attribute_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     * 
     * @return MultilingualQuery
     */
    public static function find() {
        return new MultilingualQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app/services', 'ID'),
            'service_attribute_id' => Yii::t('app/services', 'Service Attribute'),
            'value' => Yii::t('app/services', 'Value'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getProvidedServiceAttributeOptions() {
        return $this->hasMany(ProvidedServiceAttributeOption::className(), ['attribute_option_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getServiceAttribute() {
        return $this->hasOne(ServiceAttribute::className(), ['id' => 'service_attribute_id']);
    }

    /**
     * 
     * @return string
     */
    public function getFullName() {
        return $this->serviceAttribute->service->name . ' / ' . $this->serviceAttribute->name . ' / ' . $this->value;
    }

}
