<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "service_attribute_depends".
 *
 * @property int $id
 * @property int $service_attribute_id
 * @property int $depends_on_id
 * @property int $service_attribute_option_id
 *
 * @property ServiceAttribute $serviceAttribute
 * @property ServiceAttribute $dependsOn
 * @property ServiceAttributeOption $serviceAttributeOption
 */
class ServiceAttributeDepends extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'service_attribute_depends';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['service_attribute_id', 'depends_on_id', 'service_attribute_option_id'], 'integer'],
            [['service_attribute_id'], 'exist', 'skipOnError' => true, 'targetClass' => ServiceAttribute::className(), 'targetAttribute' => ['service_attribute_id' => 'id']],
            [['depends_on_id'], 'exist', 'skipOnError' => true, 'targetClass' => ServiceAttribute::className(), 'targetAttribute' => ['depends_on_id' => 'id']],
            [['service_attribute_option_id'], 'exist', 'skipOnError' => true, 'targetClass' => ServiceAttributeOption::className(), 'targetAttribute' => ['service_attribute_option_id' => 'id']],
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
            'depends_on_id' => 'Depends On ID',
            'service_attribute_option_id' => 'Service Attribute Option ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceAttribute()
    {
        return $this->hasOne(ServiceAttribute::className(), ['id' => 'service_attribute_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDependsOn()
    {
        return $this->hasOne(ServiceAttribute::className(), ['id' => 'depends_on_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceAttributeOption()
    {
        return $this->hasOne(ServiceAttributeOption::className(), ['id' => 'service_attribute_option_id']);
    }
}
