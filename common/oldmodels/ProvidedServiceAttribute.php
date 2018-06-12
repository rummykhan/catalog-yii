<?php

namespace common\oldmodels;

use common\oldmodels\ProvidedService;
use common\oldmodels\ServiceAttribute;
use common\oldmodels\Provider;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "provided_service_attribute".
 *
 * @property integer $id
 * @property integer $provided_service_id
 * @property integer $service_attribute_id
 * @property double $cost
 * @property integer $provider_id
 *
 * @property ProvidedService $providedService
 * @property Provider $provider
 * @property ServiceAttribute $serviceAttribute
 * @property ProvidedServiceAttributeOption[] $providedServiceAttributeOptions
 */
class ProvidedServiceAttribute extends ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'provided_service_attribute';
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
            [['service_attribute_id'], 'unique', 'targetAttribute' => ['provided_service_id', 'service_attribute_id'], 'message' => Yii::t("app", 'You have already added this service attribute')],
            [['provided_service_id', 'service_attribute_id'], 'required'],
            [['provided_service_id', 'service_attribute_id', 'provider_id'], 'integer'],
            [['cost'], 'number'],
            [['provided_service_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProvidedService::className(), 'targetAttribute' => ['provided_service_id' => 'id']],
            [['service_attribute_id'], 'exist', 'skipOnError' => true, 'targetClass' => ServiceAttribute::className(), 'targetAttribute' => ['service_attribute_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'provided_service_id' => Yii::t('app', 'Provided Service'),
            'service_attribute_id' => Yii::t('app', 'Service Attribute'),
            'cost' => Yii::t('app', 'Cost'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getProvidedService() {
        return $this->hasOne(ProvidedService::className(), ['id' => 'provided_service_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getServiceAttribute() {
        return $this->hasOne(ServiceAttribute::className(), ['id' => 'service_attribute_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getProvider() {
        return $this->hasOne(Provider::className(), ['id' => 'provider_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getProvidedServiceAttributeOptions() {
        return $this->hasMany(ProvidedServiceAttributeOption::className(), ['provided_service_attribute_id' => 'id']);
    }

    /**
     * 
     * @return string
     */
    public function getFullName() {
        return $this->serviceAttribute->getFullName();
    }

    /**
     * 
     * @return double
     */
    public function getCost() {
        return isset($this->cost) && $this->cost > 0 ? $this->cost : 0;
    }

}
