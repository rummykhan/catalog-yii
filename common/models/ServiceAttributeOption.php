<?php

namespace common\models;

use common\queries\NotDeletedQuery;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "service_attribute_option".
 *
 * @property int $id
 * @property int $service_attribute_id
 * @property string $name
 * @property boolean $deleted
 *
 * @property ServiceAttribute $serviceAttribute
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
            [['service_attribute_id'], 'integer'],
            [['name'], 'required'],
            [['name'], 'safe'],
            [['deleted'], 'boolean'],
            [['service_attribute_id'], 'exist', 'skipOnError' => true, 'targetClass' => ServiceAttribute::className(), 'targetAttribute' => ['service_attribute_id' => 'id']],
        ];
    }

    public static function find()
    {
        return new NotDeletedQuery(get_called_class());
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceAttribute()
    {
        return $this->hasOne(ServiceAttribute::className(), ['id' => 'service_attribute_id']);
    }
}
