<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "attribute_validation".
 *
 * @property int $id
 * @property int $service_attribute_id
 * @property int $validation_id
 *
 * @property Attribute $attribute0
 * @property Validation $validation
 */
class ServiceAttributeValidation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'service_attribute_validation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['service_attribute_id', 'validation_id'], 'integer'],
            [['service_attribute_id'], 'exist', 'skipOnError' => true, 'targetClass' => ServiceAttribute::className(), 'targetAttribute' => ['service_attribute_id' => 'id']],
            [['validation_id'], 'exist', 'skipOnError' => true, 'targetClass' => Validation::className(), 'targetAttribute' => ['validation_id' => 'id']],
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
            'validation_id' => 'Validation ID',
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
    public function getValidation()
    {
        return $this->hasOne(Validation::className(), ['id' => 'validation_id']);
    }
}
