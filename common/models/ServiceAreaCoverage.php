<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "provided_service_coverage".
 *
 * @property int $id
 * @property int $service_area_id
 * @property string $lat
 * @property string $lng
 * @property double $radius
 * @property string $coordinates
 *
 * @property ProvidedServiceArea $providedServiceArea
 */
class ServiceAreaCoverage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'service_area_coverage';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['service_area_id'], 'integer'],
            [['lat', 'lng'], 'string', 'max' => 255],
            [['service_area_id'], 'exist', 'skipOnError' => true, 'targetClass' => ServiceArea::className(), 'targetAttribute' => ['service_area_id' => 'id']],
            [['radius'], 'double']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'provided_service_area_id' => 'Provided Service Area ID',
            'lat' => 'Lat',
            'lng' => 'Lng',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceArea()
    {
        return $this->hasOne(ServiceArea::className(), ['id' => 'service_area_id']);
    }

    public function getCoordinates()
    {
        return $this->lat . ',' . $this->lng;
    }
}
