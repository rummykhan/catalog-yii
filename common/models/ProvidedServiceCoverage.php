<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "provided_service_coverage".
 *
 * @property int $id
 * @property int $provided_service_area_id
 * @property string $lat
 * @property string $lng
 * @property double $radius
 *
 * @property ProvidedServiceArea $providedServiceArea
 */
class ProvidedServiceCoverage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'provided_service_coverage';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['provided_service_area_id'], 'integer'],
            [['lat', 'lng'], 'string', 'max' => 255],
            [['provided_service_area_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProvidedServiceArea::className(), 'targetAttribute' => ['provided_service_area_id' => 'id']],
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
    public function getProvidedServiceArea()
    {
        return $this->hasOne(ProvidedServiceArea::className(), ['id' => 'provided_service_area_id']);
    }

    public function getCoordinates()
    {
        return $this->lat . ',' . $this->lng;
    }
}
