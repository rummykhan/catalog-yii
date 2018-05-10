<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "service_view_attribute".
 *
 * @property int $id
 * @property int $service_view_id
 * @property int $service_attribute_id
 *
 * @property ServiceAttribute $serviceAttribute
 * @property ServiceView $serviceView
 */
class ServiceViewAttribute extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'service_view_attribute';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['service_view_id', 'service_attribute_id'], 'integer'],
            [['service_attribute_id'], 'exist', 'skipOnError' => true, 'targetClass' => ServiceAttribute::className(), 'targetAttribute' => ['service_attribute_id' => 'id']],
            [['service_view_id'], 'exist', 'skipOnError' => true, 'targetClass' => ServiceView::className(), 'targetAttribute' => ['service_view_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'service_view_id' => 'Service View ID',
            'service_attribute_id' => 'Service Attribute ID',
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
    public function getServiceView()
    {
        return $this->hasOne(ServiceView::className(), ['id' => 'service_view_id']);
    }
}
