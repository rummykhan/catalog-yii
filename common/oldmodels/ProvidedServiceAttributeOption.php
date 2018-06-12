<?php

namespace common\oldmodels;

use common\oldmodels\ServiceAttributeOption;
use common\oldmodels\Provider;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "provided_service_attribute_option".
 *
 * @property integer $id
 * @property integer $provided_service_attribute_id
 * @property integer $attribute_option_id
 * @property double $cost
 * @property string $seller_note
 * @property integer $provider_id
 *
 * @property ProvidedServiceAttribute $providedServiceAttribute
 * @property ServiceAttributeOption $attributeOption
 * @property Provider $provider
 */
class ProvidedServiceAttributeOption extends ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'provided_service_attribute_option';
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
            [['attribute_option_id'], 'unique', 'targetAttribute' => ['provided_service_attribute_id', 'attribute_option_id'], 'message' => Yii::t("app", 'You have already added this service attribute option')],
            [['provided_service_attribute_id', 'attribute_option_id'], 'required'],
            [['provided_service_attribute_id', 'attribute_option_id', 'provider_id'], 'integer'],
            [['cost'], 'number'],
            [['seller_note'], 'string', 'max' => 255],
            [['provided_service_attribute_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProvidedServiceAttribute::className(), 'targetAttribute' => ['provided_service_attribute_id' => 'id']],
            [['attribute_option_id'], 'exist', 'skipOnError' => true, 'targetClass' => ServiceAttributeOption::className(), 'targetAttribute' => ['attribute_option_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'provided_service_attribute_id' => Yii::t('app', 'Provided Service Attribute'),
            'attribute_option_id' => Yii::t('app', 'Attribute Option'),
            'cost' => Yii::t('app', 'Cost'),
            'seller_note' => Yii::t('app', 'Seller Note'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getProvidedServiceAttribute() {
        return $this->hasOne(ProvidedServiceAttribute::className(), ['id' => 'provided_service_attribute_id']);
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
    public function getAttributeOption() {
        return $this->hasOne(ServiceAttributeOption::className(), ['id' => 'attribute_option_id']);
    }

    public function getAttributeOptionValue() {
        return $this->attributeOption->value;
    }

    /**
     * 
     * @return string
     */
    public function getFullName() {
        return $this->attributeOption->getFullName();
    }

    /**
     * 
     * @return double
     */
    public function getCost() {
        return isset($this->cost) && $this->cost > 0 ? $this->cost : 0;
    }

}
