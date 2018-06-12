<?php

namespace common\oldmodels;

use common\components\eventhandler\EventTracker;
use common\components\MailComposer;
use common\helpers\ModelHelper;
use common\helpers\OptionsBehavior;
use common\models\servicerequests\ServiceRequest;
use common\oldmodels\Service;
use common\oldmodels\Provider;
use common\models\settings\SystemSetting;
use omgdef\multilingual\MultilingualBehavior;
use omgdef\multilingual\MultilingualQuery;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "provided_service".
 *
 * @property integer $id
 * @property integer $provider_id
 * @property integer $service_id
 * @property integer $enable
 * @property double $cost
 * @property integer $in_house
 * @property integer $collect_and_return
 * @property double $in_house_price
 * @property double $collect_and_return_price
 * @property integer $collect_and_return_time
 * @property integer $preferred_courier
 * @property integer $minimum_cost
 * @property integer $min_accepted
 * @property integer $max_accepted
 * @property boolean $express_order
 * @property integer $express_order_charge
 * @property integer $sla_accept
 * @property integer $sla_start
 * @property integer $sla_collect
 * @property integer $sla_return
 * @property integer $sla_complete
 * @property integer $hours_in_advance
 * @property integer $commission_rate
 * @property boolean $same_day_booking
 * @property integer $daily_cap
 * @property string $note_description
 * @property string $created_at
 * @property string $updated_at
 * @property string $provider_notes
 * @property string $hourly_cap
 *
 * @property Provider $provider
 * @property Service $service
 * @property ProvidedServiceAttribute[] $providedServiceAttributes
 */
class ProvidedService extends ActiveRecord
{

    const STATUS_INACTIVE = 10;
    const STATUS_ACTIVE = 20;

    const PREFERRED_COURIER_HELPBIT = 10; // wing.ae
    const PREFERRED_COURIER_OTHER = 90; // internal fleet

    const SCENARIO_PROVIDER_UPDATE = 'PROVIDER_UPDATE';
    const EVENT_ATTRIBUTE_PRICE_CHANGE = 'ATTRIBUTE_PRICE_CHANGE';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'provided_service';
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
            [['service_id'], 'unique', 'targetAttribute' => ['provider_id', 'service_id'], 'message' => Yii::t("app", 'You have already added this service')],
            [['provider_id', 'service_id'], 'required'],
            [['provider_id', 'service_id', 'enable', 'collect_and_return_time', 'preferred_courier'], 'integer'],
            [['collect_and_return_time'], 'integer', 'max' => 30],

            [['commission_rate'], 'number', 'min' => 0, 'max' => 100],

            [['in_house', 'collect_and_return', 'same_day_booking'], 'integer'],
            [['in_house_price', 'collect_and_return_price'], 'number', 'min' => 0],
            [['cost', 'minimum_cost', 'min_accepted', 'max_accepted'], 'number', 'min' => 0],
            [['hours_in_advance'], 'number', 'min' => 1, 'max' => 48],

            [['provider_id'], 'exist', 'skipOnError' => true, 'targetClass' => Provider::className(), 'targetAttribute' => ['provider_id' => 'id']],
            [['service_id'], 'exist', 'skipOnError' => true, 'targetClass' => Service::className(), 'targetAttribute' => ['service_id' => 'id']],
            ['express_order', 'boolean'],
            ['express_order_charge', 'integer'],
            [['sla_accept', 'sla_start', 'sla_collect', 'sla_return', 'sla_complete', 'hours_in_advance', 'commission_rate'], 'integer'],
            [['daily_cap'], 'integer'],
            [['note_description'], 'safe'],
            [['provider_notes'], 'string'],
            [['provider_sms_text'], 'safe'],
            //[['minimum_cost'], 'required'],
            [['preferred_courier'], 'default', 'value' => static::PREFERRED_COURIER_OTHER],
            ['hourly_cap', 'integer']
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[static::SCENARIO_PROVIDER_UPDATE] = ['enable', 'minimum_cost', 'in_house', 'collect_and_return'];
        return $scenarios;
    }

    public function checkCollectAndReturnTime($attributes, $params)
    {
        if ($this->collect_and_return == 1) {
            if (!isset($this->collect_and_return_time) || $this->collect_and_return_time <= 0) {
                $this->addError('collect_and_return_time', 'Please select the average service time');
            }
        }
    }

    public function behaviors()
    {
        return [
            'status' => [
                'class' => OptionsBehavior::className(),
                'attribute' => 'enable',
                'options' => [
                    self::STATUS_ACTIVE => Yii::t("app", "Active"),
                    self::STATUS_INACTIVE => Yii::t("app", "Inactive"),
                ]
            ],
            'courier' => [
                'class' => OptionsBehavior::className(),
                'attribute' => 'preferred_courier',
                'options' => [
                    self::PREFERRED_COURIER_OTHER => Yii::t("app", "Other"),
                    self::PREFERRED_COURIER_HELPBIT => Yii::t("app", "Helpbit courier service")
                ]
            ],
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ],
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
            'service_id' => Yii::t('app', 'Service ID'),
            'enable' => Yii::t('app', 'Enable'),
            'cost' => Yii::t('app', 'Cost'),
            'in_house_price' => Yii::t('app', 'Onsite Extra Cost'),
            'collect_and_return_price' => Yii::t('app', 'Collect & Return Extra Cost'),
            'collect_and_return_time' => Yii::t('app', 'Collect & Return Time (days)'),
            'preferred_courier' => Yii::t('app', 'Preferred Courier'),
            'express_order' => Yii::t('app', 'One Click Order'),
            'express_order_charge' => Yii::t('app', 'One click order extra charge'),
            'sla_accept' => 'SLA Acceptance (Hours)',
            'sla_start' => 'SLA Start (Hours)',
            'sla_collect' => 'SLA Collection (Hours)',
            'sla_return' => 'SLA Completion (Hours)',
            'sla_complete' => 'SLA Return (Hours)',
            'hours_in_advance' => 'Pre-scheduling buffer time (in hours)',
            'same_day_booking' => 'Allow Same day booking',
            'daily_cap' => Yii::t('app', 'Daily Capacity (No. of Services per Day)'),
            'provider_notes' => Yii::t('app', 'Provider Notes'),
            'provider_sms_text' => Yii::t('app', 'SMS Text'),
            'hourly_cap' => Yii::t('app', 'Hourly Capacity (No. of Services per Hour)')
        ];
    }

    public function attributeHints()
    {

        $min_hours = SystemSetting::getValue("minimum_order_hours");

        return [
            'cost' => Yii::t("app", 'A fixed price regadless of service options'),
            'minimum_cost' => Yii::t("app", "If the price of customer's order is lower than this amount, The customer will be charged this amount."),
            'commission_rate' => Yii::t('app', 'If commission rate not set, so will get the value from service'),
            'hours_in_advance' => Yii::t('app', 'If Not set the value will be') . ' ' . $min_hours . ' ' . Yii::t('app', 'Hours')
        ];
    }


    /**
     * @inheritdoc
     *
     * @return MultilingualQuery
     */
    public static function find()
    {
        return new MultilingualQuery(get_called_class());
    }

    /**
     * @return ActiveQuery
     */
    public function getProvider()
    {
        return $this->hasOne(Provider::className(), ['id' => 'provider_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getService()
    {
        return $this->hasOne(Service::className(), ['id' => 'service_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getProvidedServiceAttributes()
    {
        return $this->hasMany(ProvidedServiceAttribute::className(), ['provided_service_id' => 'id']);
    }

    /**
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->service->full_name;
    }

    /**
     *
     * @return double
     */
    public function getCost()
    {
        return isset($this->cost) && $this->cost > 0 ? $this->cost : 0;
    }

    /**
     *
     * @param integer $service_id
     * @param integer $provider_id
     * @return ProvidedService
     */
    public static function findByServiceAndProvider($service_id, $provider_id)
    {
        return static::findOne(['service_id' => $service_id, 'provider_id' => $provider_id]);
    }

    public function getCommissionRate()
    {
        if (isset($this->commission_rate)) {
            return $this->commission_rate;
        }
        return $this->service->getCommissionRate();
    }

    public function getCommissionRateText()
    {
        if (isset($this->commission_rate)) {
            return "Custom Provider Commission " . "($this->commission_rate%)";
        }
        return $this->service->getCommissionRateText(true);
    }

    public function getSameDayBookingAdvanceHours()
    {
        if ($this->same_day_booking) {
            if (isset($this->hours_in_advance)) {
                return $this->hours_in_advance;
            } else {
                return SystemSetting::getValue("minimum_order_hours");
            }
        }

        return false;
    }


    public function getBufferTime()
    {
        if (isset($this->hours_in_advance)) {
            return $this->hours_in_advance;
        }

        return SystemSetting::getValue("minimum_order_hours");
    }

    /**
     * Notify price change to marketing team
     *
     * @param array $attributes
     * @return mixed|boolean
     */
    public function fireCostEvents($attributes)
    {
        $priceChangedFrom = intval($attributes['minimum_cost']);
        $priceChangedTo = intval($this->minimum_cost);

        if ($priceChangedFrom === $priceChangedTo) {
            return false;
        }

        $model = $this;
        $composer = new MailComposer();
        $composer->setTemplate('provided-service-price-changed')
            ->setSubject('Provided service price changed.')
            ->setTo(Yii::$app->params['toNotifyemails'])
            ->setFrom(Yii::$app->params['noReplyEmail'])
            ->send(compact('priceChangedFrom', 'priceChangedTo', 'model'));

        $composer->setTemplate('provided-service-price-changed')
            ->setSubject('Provided service price changed.')
            ->setTo(Yii::$app->params['toNotifyemails2'])
            ->setFrom(Yii::$app->params['noReplyEmail'])
            ->send(compact('priceChangedFrom', 'priceChangedTo', 'model'));

        $composer->setTemplate('provided-service-price-changed')
            ->setSubject('Provided service price changed.')
            ->setTo(Yii::$app->params['toNotifyemails3'])
            ->setFrom(Yii::$app->params['noReplyEmail'])
            ->send(compact('priceChangedFrom', 'priceChangedTo', 'model'));
    }

    public function attachInsertEvents()
    {
        $this->on(static::EVENT_AFTER_INSERT, function ($event) {
            /** @var static $service */
            $service = $event->sender;

            $service->preferred_courier = static::PREFERRED_COURIER_OTHER;
            $service->save(false);
        });
    }

    /**
     * @param int $provider_id
     * @param array $services
     * @return mixed
     */
    public static function createServicesForProvider($provider_id, $services)
    {
        foreach ($services as $service) {

            $model = new ProvidedService();
            $model->provider_id = $provider_id;
            $model->service_id = $service;
            $model->save();

            if ($model->hasErrors()) {
                Yii::$app->getSession()->addFlash('error', ModelHelper::getErrors($model));
            }
        }
    }

    public function hasServices()
    {
        return ServiceRequest::find()
                ->where(['provider_id' => $this->provider_id])
                ->andWhere(['service_id' => $this->service_id])
                ->count() > 0;
    }

}
