<?php

namespace common\oldmodels;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "provider_covered_area".
 *
 * @property integer $id
 * @property integer $provider_id
 * @property string $coordinates
 * @property double $radius
 *
 * @property Provider $provider
 */
class ProviderCoveredArea extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'provider_covered_area';
    }

    public static function getDB()
    {
        return Yii::$app->get("old");
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['provider_id'], 'integer'],
            [['radius'], 'number'],
            [['coordinates'], 'string', 'max' => 255],
            [['provider_id'], 'exist', 'skipOnError' => true, 'targetClass' => Provider::className(), 'targetAttribute' => ['provider_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'provider_id' => Yii::t('app', 'Provider ID'),
            'coordinates' => Yii::t('app', 'Coordinates'),
            'radius' => Yii::t('app', 'Radius'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getProvider()
    {
        return $this->hasOne(Provider::className(), ['id' => 'provider_id']);
    }

}
