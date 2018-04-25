<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\db\Query;

/**
 * This is the model class for table "provided_service".
 *
 * @property int $id
 * @property int $service_id
 * @property int $provider_id
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Provider $provider
 * @property Service $service
 */
class ProvidedService extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'provided_service';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['service_id', 'provider_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['provider_id'], 'exist', 'skipOnError' => true, 'targetClass' => Provider::className(), 'targetAttribute' => ['provider_id' => 'id']],
            [['service_id'], 'exist', 'skipOnError' => true, 'targetClass' => Service::className(), 'targetAttribute' => ['service_id' => 'id']],
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()')
            ]
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
            'provider_id' => 'Provider ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProvider()
    {
        return $this->hasOne(Provider::className(), ['id' => 'provider_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getService()
    {
        return $this->hasOne(Service::className(), ['id' => 'service_id']);
    }

    public function getUnProvidedServicesList()
    {
        $providedServices = collect(
            static::find()
                ->select([new Expression('service_id as id')])
                ->where(['provider_id' => $this->provider_id])
                ->all()
        )->pluck('id')->toArray();


        return collect(
            Service::find()->where(['NOT IN', 'id', $providedServices])->asArray()->all()
        )->pluck('name', 'id');
    }
}
