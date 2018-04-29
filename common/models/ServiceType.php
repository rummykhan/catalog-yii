<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "service_type".
 *
 * @property int $id
 * @property string $type
 *
 * @property ProvidedServiceType[] $providedServiceTypes
 */
class ServiceType extends \yii\db\ActiveRecord
{
    const TYPE_IN_HOUSE = 'In House';
    const TYPE_COLLECT_AND_RETURN = 'Collect & Return';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'service_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProvidedServiceTypes()
    {
        return $this->hasMany(ProvidedServiceType::className(), ['service_type_id' => 'id']);
    }
}
