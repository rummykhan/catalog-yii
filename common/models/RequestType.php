<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "service_type".
 *
 * @property int $id
 * @property string $name
 *
 * @property ProvidedRequestType[] $providedServiceTypes
 */
class RequestType extends \yii\db\ActiveRecord
{
    const TYPE_IN_HOUSE = 'In House';
    const TYPE_COLLECT_AND_RETURN = 'Collect & Return';
    const TYPE_WALK_IN = 'Walk In';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'request_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Type',
        ];
    }

    public static function toList()
    {
        return collect(static::find()->all())->pluck('name', 'id');
    }

    public function getServiceRequestType()
    {
        return $this->hasMany(ServiceRequestType::className(), ['request_type_id' => 'id']);
    }
}
