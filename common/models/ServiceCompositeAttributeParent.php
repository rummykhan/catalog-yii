<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "service_composite_attribute_parent".
 *
 * @property int $id
 * @property int $service_id
 *
 * @property ServiceCompositeAttribute[] $serviceCompositeAttributes
 * @property ServiceCompositeAttributeChild[] $serviceCompositeAttributeChildren
 * @property Service $service
 */
class ServiceCompositeAttributeParent extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'service_composite_attribute_parent';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['service_id'], 'integer'],
            [['service_id'], 'exist', 'skipOnError' => true, 'targetClass' => Service::className(), 'targetAttribute' => ['service_id' => 'id']],
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceCompositeAttributes()
    {
        return $this->hasMany(ServiceCompositeAttribute::className(), ['service_composite_attribute_parent_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceCompositeAttributeChildren()
    {
        return $this->hasMany(ServiceCompositeAttributeChild::className(), ['service_composite_attribute_parent_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getService()
    {
        return $this->hasOne(Service::className(), ['id' => 'service_id']);
    }
}
