<?php

namespace common\models;

use common\helpers\OptionsBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "provider".
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $username
 * @property string $password
 * @property string $email
 * @property integer $status
 * @property integer $country_id
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Calendar[] $calendars
 * @property Country $country
 */
class Provider extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 10;
    const STATUS_IN_ACTIVE = 20;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'provider';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at'], 'safe'],
            [['username', 'first_name', 'last_name', 'password', 'email'], 'string', 'max' => 255],
            [['username'], 'unique'],
            [['email'], 'unique'],
            [['status', 'country_id',], 'integer']
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()')
            ],
            [
                'class' => OptionsBehavior::className(),
                'attribute' => 'status',
                'options' => [
                    static::STATUS_ACTIVE => 'Active',
                    static::STATUS_IN_ACTIVE => 'In Active',
                ]
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
            'username' => 'Username',
            'password' => 'Password',
            'email' => 'Email',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getCalendars()
    {
        return $this->hasMany(Calendar::className(), ['provider_id' => 'id']);
    }

    public function getServiceAreas()
    {
        return $this->hasMany(ServiceArea::className(), ['provider_id' => 'id']);
    }

    public function getCountry()
    {
        return $this->hasOne(Country::className(), ['id' => 'country_id']);
    }

    public function getCalendarsList()
    {
        return collect($this->getCalendars()->asArray()->all())->pluck('name', 'id')->toArray();
    }

    public function getServiceAreasList()
    {
        return collect($this->getServiceAreas()->asArray()->all())->pluck('name', 'id')->toArray();
    }
}
