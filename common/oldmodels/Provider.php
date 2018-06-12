<?php

namespace common\oldmodels;

use admin\models\provider\ProviderGroup;
use admin\models\provider\ProviderGroupMember;
use common\components\distance\DistanceHelper;
use common\components\distance\LocationHelper;
use common\components\eventhandler\EventTracker;
use common\components\MailComposer;
use common\helpers\arr\Collection;
use common\helpers\Countries;
use common\helpers\OptionsBehavior;
use common\models\AbstractUser;
use common\models\badge\Badge;
use common\oldmodels\ProvidedService;
use common\oldmodels\ProvidedServiceAttribute;
use common\oldmodels\ProvidedServiceAttributeOption;
use common\models\servicerequests\ServiceRequest;
use common\models\servicerequests\SwitchableRequest;
use common\oldmodels\Service;
use common\models\users\Account;
use common\models\promotion\Promotion;
use common\models\settings\SystemSetting;
use common\models\promotion\ProviderPromotion;
use common\models\promotion\ServiceAttributeItemPromotion;

use common\helpers\ServiceAttributeTypeBasket;
use common\models\services\ServiceAttribute;


use common\models\users\provider\SuspensionReason;
use customernew\v1\helpers\schedule\Manager;
use omgdef\multilingual\MultilingualBehavior;
use omgdef\multilingual\MultilingualQuery;
use common\models\users\provider\TeamMember;
use yii\base\Event;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use common\models\badge\ProviderBadge;

use Imagine\Image\ImageInterface;
use Yii;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\Json;
use yii\mail\MailerInterface;
use yiidreamteam\upload\FileUploadBehavior;
use yiidreamteam\upload\ImageUploadBehavior;
use zxbodya\yii2\imageAttachment\ImageAttachmentBehavior;

/**
 * This is the model class for table "provider".
 *
 * @property integer $id
 * @property integer $account_id
 * @property string $username
 * @property string $email
 * @property string $password
 * @property integer $status
 * @property string $password_reset_token
 * @property string $auth_key
 * @property string $firstname
 * @property string $lastname
 * @property integer $gender
 * @property string $birthdate
 * @property string $trade_licence_number
 * @property string $phone
 * @property string $building
 * @property string $building_number
 * @property string $street
 * @property string $postcode
 * @property string $locality
 * @property string $city
 * @property string $region
 * @property string $country
 * @property string $coordinates
 * @property string $schedule
 * @property string $current_week_schedule
 * @property double $covered_area
 * @property string $created_at
 * @property string $updated_at
 * @property string $bank_name
 * @property string $iban
 * @property string $swift_code
 * @property string $location
 * @property string $seller_note
 * @property string $can_use_other_courier
 *
 * @property boolean $can_cod
 * @property boolean $is_laundry
 * @property string $alt_phone
 * @property string $access_token
 * @property string $regid
 * @property string $platform
 *
 * @property string $billing_name
 * @property string $billing_country
 * @property string $billing_region
 * @property string $billing_city
 * @property string $billing_address
 * @property string $billing_poscode
 * @property string $bank_branch
 * @property string $bank_account_name
 * @property string $bank_account_id
 * @property integer $payment_cycle
 * @property boolean $can_view_customer_info
 * @property boolean $switchable
 * @property boolean $can_use_internal_ticket
 * @property string $timezone
 * @property string $currency
 * @property string $vat_registeration_id
 * @property string $vat_registeration_document_filename
 * @property string $trade_license_document_filename
 *
 * @property ProvidedService[] $providedServices
 * @property ProvidedServiceAttribute[] $providedServicesAttributes
 * @property ProvidedServiceAttributeOption[] $providedServicesAttributesOptions
 * @property Account $account
 * @property Quotation[] $quotations
 * @property ServiceRequest[] $serviceRequests
 * @property ProviderSession[] $providerSessions
 * @property ProviderCoveredArea[] $providerCoveredAreas
 * @property SwitchableRequest[] $switchableRequests
 * @property SuspensionReason[] $suspensionReasons
 * @property ProviderGroupMember $groupInfo
 * @property ProviderGroup $group
 */
class Provider extends ActiveRecord
{

    const GENDER_FEMALE = 20;
    const GENDER_MALE = 10;
    const PAYMENT_CYCLE_A = 10;
    const PAYMENT_CYCLE_B = 20;

    const SCENARIO_SCHEDULE = 'SCENARIO_SCHEDULE_UPDATE';
    const SCENARIO_PAYMENT_INFO = 'SCENARIO_PAYMENT_INFO_UPDATE';
    const SCENARIO_CREATE = 'SCENARIO_CREATE';

    public static function getDB()
    {
        return Yii::$app->get("old");
    }

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
            [['firstname', 'lastname', /* 'gender', 'birthdate' */], 'required'],
            [['gender', 'payment_cycle', 'vat_registeration_id'], 'integer'],
            [['birthdate'], 'safe'],
            [['vat_registeration_document_filename', 'trade_license_document_filename'], 'file', 'extensions' => 'pdf,jpg,jpeg,png,docx', 'maxSize' => (1024 * 1024) * 2],
            [['timezone'], 'default', 'value' => 'Asia/Dubai'],
            [['currency'], 'default', 'value' => 'aed'],
            [['covered_area'], 'number'],
            [['image'], 'file', 'extensions' => 'jpg,png,jpeg', 'maxSize' => (1024 * 1024) * 2],
            [['firstname', 'lastname', 'phone', 'alt_phone', 'building', 'building_number', 'street', 'postcode', 'locality', 'city', 'region', 'country', 'coordinates', 'trade_licence_number', 'timezone', 'currency'], 'string', 'max' => 255],
            [['schedule', 'current_week_schedule', 'location'], 'string', 'max' => 1000],
            [['seller_note'], 'string', 'max' => 255],
            [['seller_note_ar'], 'string', 'max' => 255],
            [["billing_name", "billing_country", "billing_region", "billing_city", "billing_address", "billing_poscode"], 'string', 'max' => 255],
            [["bank_branch", "bank_account_name", "bank_account_id"], 'string', 'max' => 255],
            [['bank_name', 'iban', 'swift_code'], 'string', 'max' => 255],
            [['is_laundry', 'can_use_other_courier', 'can_cod', 'can_use_internal_ticket'], 'boolean'],
            [['is_laundry', 'can_use_internal_ticket'], 'default', 'value' => false],
            //['country', 'in', 'range' => array_keys(Countries::$list)],
            ['coordinates', 'validateCountry'],
            ['location', 'validateAddress'],
            [['can_view_customer_info', 'switchable'], 'boolean'],
            [['username'], 'string', 'length' => [3, 26]],
            [['username_ar'], 'string', 'length' => [3, 26]],
            [[
                'username',
                'username_ar',
                'email',
                'password',
                'image',
                'firstname',
                'lastname',
                'phone',
                'birthdate'
            ], 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process'],
            [['can_use_other_courier'], 'default', 'value' => true]
        ];
    }

    public function validateCountry($attribute, $params)
    {
        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng={$this->$attribute}&key=" . \Yii::$app->params['googleMapsKey'];
        $content = Json::decode(file_get_contents($url));
        $component = $content['results'][0]['address_components'];
        foreach ($component as $key => $comp) {
            if (isset($comp['types']) && in_array('country', $comp['types'])) {
                if (!array_key_exists(strtoupper($comp['short_name']), Countries::$list)) {
                    $this->addError("country", Yii::t("app", "Sorry, we are currently available in {countries} only.", ['countries' => implode(", ", Countries::$list)]));
                } else {
                    break;
                }
            }
        }
    }

    public function is14NumbersOnly($attribute)
    {
        if (!preg_match('/^[0-9]{14}$/', $this->$attribute)) {
            $this->addError($attribute, 'must contain exactly 14 digits.');
        }
    }

    public function validateAddress($attribute, $params)
    {
        if (!LocationHelper::validateAddress($this->$attribute)) {
            $this->addError($attribute, \Yii::t("app", "Please select a location and don't type it"));
        }
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'gender' => [
                'class' => OptionsBehavior::className(),
                'attribute' => 'gender',
                'options' => [
                    self::GENDER_FEMALE => Yii::t("app", "Female"),
                    self::GENDER_MALE => Yii::t("app", "Male"),
                ]
            ],
            'payment_cycle' => [
                'class' => OptionsBehavior::className(),
                'attribute' => 'payment_cycle',
                'options' => [
                    self::PAYMENT_CYCLE_A => Yii::t("app", "Payment Cycle A"),
                    self::PAYMENT_CYCLE_B => Yii::t("app", "Payment Cycle B")
                ]
            ],
            [
                'class' => ImageUploadBehavior::className(),
                'attribute' => 'image',
                'thumbs' => [
                    'thumb' => ['width' => 250, 'height' => 250],
                ],
                'filePath' => '@uploadRoot/images/pro/pro_[[pk]].[[extension]]',
                'fileUrl' => '@uploadWeb/images/pro/pro_[[pk]].[[extension]]',
                'thumbPath' => '@uploadRoot/images/pro/[[profile]]_pro_[[pk]].[[extension]]',
                'thumbUrl' => '@uploadWeb/images/pro/[[profile]]_pro_[[pk]].[[extension]]',
            ],
            'vat_document' => [
                'class' => FileUploadBehavior::className(),
                'attribute' => 'vat_registeration_document_filename',
                'filePath' => '@uploadRoot/vat_registeration/provider/vat_registeration_document_[[pk]].[[extension]]',
                'fileUrl' => '@uploadWeb/vat_registeration/provider/vat_registeration_document_[[pk]].[[extension]]',
            ],
            'trade_license_document' => [
                'class' => FileUploadBehavior::className(),
                'attribute' => 'trade_license_document_filename',
                'filePath' => '@uploadRoot/trade_license/provider/trade_license_document_[[pk]].[[extension]]',
                'fileUrl' => '@uploadWeb/trade_license/provider/trade_license_document_[[pk]].[[extension]]',
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'firstname' => Yii::t('app', 'Firstname'),
            'lastname' => Yii::t('app', 'Lastname'),
            'gender' => Yii::t('app', 'Gender'),
            'birthdate' => Yii::t('app', 'Birthdate'),
            'phone' => Yii::t('app', 'Phone'),
            'alt_phone' => Yii::t('app', 'Alternative Phone'),
            'building' => Yii::t('app', 'Building'),
            'building_number' => Yii::t('app', 'Building Number'),
            'street' => Yii::t('app', 'Street'),
            'postcode' => Yii::t('app', 'Postcode'),
            'locality' => Yii::t('app', 'Locality'),
            'city' => Yii::t('app', 'City'),
            'region' => Yii::t('app', 'Region'),
            'country' => Yii::t('app', 'Country'),
            'coordinates' => Yii::t('app', 'Coordinates'),
            'schedule' => Yii::t('app', 'Availabilty Schedule'),
            'current_week_schedule' => Yii::t('app', 'Current Week Availabilty Schedule'),
            'covered_area' => Yii::t('app', 'Covered Area (meters)'),
            'can_cod' => Yii::t('app', 'Can do cash on delivery'),
            'billing_name' => Yii::t('app', 'Company Name'),
            'billing_country' => Yii::t('app', 'Country'),
            'billing_region' => Yii::t('app', 'Region/State/Province'),
            'billing_city' => Yii::t('app', 'City'),
            'billing_address' => Yii::t('app', 'Address'),
            'billing_poscode' => Yii::t('app', 'Zip/Postal Code'),
            'can_use_internal_ticket' => Yii::t('app', 'Can Use Internal Ticket'),
            'trade_licence_number' => Yii::t('app', 'Trade Licence Number'),
            'can_use_other_courier' => Yii::t('app', 'Can use internal fleet'),
            'vat_registeration_id' => Yii::t('app', 'VAT Registeration ID'),
            'vat_registeration_document_filename' => Yii::t('app', 'Vat Document'),
            'trade_license_document_filename' => Yii::t('app', 'Trade License Document'),
            'username_ar' => Yii::t('app', 'Username - Arabic'),
            'seller_note_ar' => Yii::t('app', 'Seller note - Arabic'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return array_merge(parent::attributeHints(), [
            //'coordinates' => 'Updating Location on map will trigger the inputs bellow to update'
        ]);
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[static::SCENARIO_SCHEDULE] = ['schedule', 'current_week_schedule'];
        $scenarios[static::SCENARIO_PAYMENT_INFO] = [
            'billing_name',
            'billing_country',
            'billing_region',
            'billing_city',
            'billing_address',
            'billing_poscode',
            'bank_branch',
            'bank_account_name',
            'bank_account_id',
            'bank_name',
            'iban',
            'swift_code'
        ];

        $scenarios[static::SCENARIO_CREATE] = [
            'username', 'email', 'password', 'can_cod', 'can_use_other_courier', 'can_view_customer_info',
            'switchable', 'can_use_internal_ticket', 'firstname', 'lastname', 'gender', 'birthdate',
            'trade_licence_number', 'vat_registeration_id', 'vat_registeration_document_filename',
            'trade_license_document_filename', 'phone', 'alt_phone', 'seller_note', 'location',
            'street', 'city', 'region', 'postcode', 'country', 'locality', 'coordinates', 'building', 'building_number'
        ];

        return $scenarios;
    }

    /**
     * @return ActiveQuery
     */
    public function getProvidedServices()
    {
        return $this->hasMany(ProvidedService::className(), ['provider_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getServiceRequests()
    {
        return $this->hasMany(ServiceRequest::className(), ['provider_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getProvidedServicesAttributes()
    {
        return $this->hasMany(ProvidedServiceAttribute::className(), ['provided_service_id' => 'id'])
            ->via("providedServices");
    }

    /**
     * @return ActiveQuery
     */
    public function getProvidedServicesAttributesOptions()
    {
        return $this->hasMany(ProvidedServiceAttributeOption::className(), ['provided_service_attribute_id' => 'id'])
            ->via("providedServicesAttributes");
    }

    /**
     * @return ActiveQuery
     */
    public function getAccount()
    {
        return $this->hasOne(Account::className(), ['id' => 'account_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getProviderSessions()
    {
        return $this->hasMany(ProviderSession::className(), ['provider_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getQuotations()
    {
        return $this->hasMany(Quotation::className(), ['provider_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getSwitchableRequests()
    {
        return $this->hasMany(SwitchableRequest::className(), ['provider_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getBadges()
    {
        return $this->hasMany(Badge::className(), ['id' => 'badge_id'])
            ->viaTable('provider_badge', ['provider_id' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return Account::TYPE_PROVIDER;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if (!$insert) {
            if (isset($changedAttributes['schedule']) &&
                $changedAttributes['schedule'] != $this->schedule
            ) {
                /* @var $mail MailerInterface */
                $mail = Yii::$app->mailer->compose(['html' => 'provider-updated-notification'], [
                    'notification' => "{$this->username} updated his availability schedule",
                ]);
                $mail->setFrom([Yii::$app->params['noReplyEmail'] => Yii::$app->params['noReplyName']]);
                $mail->setTo(Yii::$app->params['toNotifyemails2']);
                $mail->setSubject("{$this->username} updated his availability schedule");
                $mail->send();
            }
        }
    }

    public function getAvgRating($serviceId)
    {
        $query = new Query();
        $all = $query->select(['avg(customer_rating) avg'])
            ->from(['service_request'])
            ->where(['status' => ServiceRequest::STATUS_FINISHED_COMPLETED_CONFIRMED])
            ->andWhere(['provider_id' => $this->id])
            ->andWhere(['service_id' => $serviceId])
            ->all();
        return isset($all[0]['avg']) && $all[0]['avg'] > 0 ? round($all[0]['avg'], 2) : 5;
    }

    public function getAvgRatingWithCount($serviceId)
    {
        $query = new Query();
        $all = $query->select(['avg(customer_rating) avg,count(customer_rating) rating_count'])
            ->from(['service_request'])
            ->where(['status' => ServiceRequest::STATUS_FINISHED_COMPLETED_CONFIRMED])
            ->andWhere(['provider_id' => $this->id])
            ->andWhere(['service_id' => $serviceId])
            ->all();
        $result = [];
        $result['avg'] = isset($all[0]['avg']) && $all[0]['avg'] > 0 ? round($all[0]['avg'], 2) : 5;
        $result['count'] = $all[0]['rating_count'];
        return $result;
    }


    /**
     *
     * @param type $serviceId
     * @return array('avg','count')
     */
    public function getAvgCategoryRating($serviceId)
    {
        $service = Service::findOne($serviceId);
        //$siblingSerivces = $service->parent->services;
        $query = new Query();
        $all = $query->select(['avg(customer_rating) avg, count(id) as count'])
            ->from(['service_request'])
            ->where(['status' => ServiceRequest::STATUS_FINISHED_COMPLETED_CONFIRMED])
            ->andWhere(['provider_id' => $this->id])
            //->andWhere(['service_id' => ArrayHelper::getColumn($siblingSerivces, 'id', false)])
            ->all();
        $all[0]['avg'] = isset($all[0]['avg']) ? round($all[0]['avg'], 2) : 0;
        $all[0]['count'] = isset($all[0]['count']) ? $all[0]['count'] : 0;
        $sum = $all[0]['count'] * $all[0]['avg'] + 5 + 5 + 5; //add 3, 5 stars reviews
        $all[0]['count'] = $all[0]['count'] + 3; //add 3, 5 stars reviews
        $all[0]['avg'] = $sum / $all[0]['count']; //add 3, 5 stars reviews
        return $all[0];
    }

    public function stripData()
    {
        unset($this->bank_name);
        unset($this->iban);
        unset($this->swift_code);
        unset($this->auth_key);
        unset($this->password);
        unset($this->password_reset_token);
        unset($this->updated_at);
        unset($this->created_at);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        /* @var $session ProviderSession */
        $session = ProviderSession::find()
            ->where(['access_token' => $token])
            ->one();
        if (isset($session) && $session->provider->status == static::STATUS_ACTIVE) {
            $session->last_accessed = new Expression('NOW()');
            $session->save(false);
            $provider = $session->provider;
            $provider->access_token = $session->access_token;
            $provider->regid = $session->regid;
            $provider->platform = $session->platform;
            return $provider;
        }
        return null;
        //return static::find()
        //                ->where(['access_token' => $token])
        //                ->andWhere(['status' => static::STATUS_ACTIVE])
        //                ->one();
    }

    /**
     * generate a random access token for the user
     */
    public function generateAccessToken()
    {
        $this->access_token = Yii::$app->getSecurity()->generateRandomString() . '_' . $this->id . '_' . time();
    }

    public function canUseCreditCard()
    {
        if ($this->country == 'SA') {
            return false;
        }

        return true;
    }

    /**
     * clear he current access token of the user
     */
    public function clearAccessToken()
    {
        $this->access_token = null;
    }

    /**
     * Create a new session for this provider
     */
    public function createNewSession()
    {
        $session = new ProviderSession();
        $session->provider_id = $this->id;
        $session->generateAccessToken();
        $session->save();
        return $session;
    }

    public function destroySession($token)
    {
        ProviderSession::deleteAll([
            'access_token' => $token
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getProviderCoveredAreas()
    {
        return $this->hasMany(ProviderCoveredArea::className(), ['provider_id' => 'id']);
    }


    public function setCurrencyAndTimezone()
    {
        $this->detachBehavior("vat_document");
        $this->detachBehavior("trade_license_document");
        if ($this->country == 'SA') {
            $this->currency = 'sar';
            $this->timezone = 'Asia/Riyadh';
        }

        if ($this->country == 'AE') {
            $this->currency = 'aed';
            $this->timezone = 'Asia/Dubai';
        }

        $this->save(false);
    }

    public function setIpAddress()
    {
        $this->ip_address = Yii::$app->getRequest()->getUserIP();
    }

    public function getServices()
    {
        $results = (new Query())
            ->select(['hdsc2.name'])
            ->from('provider hdp')
            ->join('INNER JOIN', 'provided_service hdps', 'hdp.id = hdps.provider_id')
            ->join('INNER JOIN', 'service hds', 'hdps.service_id = hds.id')
            ->join('INNER JOIN', 'service_category hdsc', 'hds.parent_id = hdsc.id')
            ->join('INNER JOIN', 'service_category hdsc2', 'hdsc.parent_id = hdsc2.id')
            ->where(['hdsc2.parent_id' => null, 'hdp.id' => $this->id])
            ->distinct()
            ->all();

        $services = array_map(function ($result) {
            return $result['name'];
        }, $results);

        return implode(', ', $services);
    }

    public function getAutoPromotion($params)
    {
        $providerPromo = ProviderPromotion::find()->where(['provider_id' => $params['provider']])->all();

        $ids = ArrayHelper::getColumn($providerPromo, 'promotion_id');

        $automaticPromotions = Promotion::find()
            ->where(['in', 'id', $ids])
            ->andWhere(['is_active' => 1])
            ->andWhere(['is_automatic' => 1])
            ->all();

        if (isset($automaticPromotions) && !empty($automaticPromotions) && is_array($automaticPromotions)) {
            $bestDiscount = 0;
            $bestPromotion = null;
            foreach ($automaticPromotions as $key => $promotion) {
                if ($promotion->isApplicable($params)) {
                    $discountAmount = $promotion->calculateDiscountAmount($params['cost']);
                    if ($discountAmount > $bestDiscount) {
                        $bestDiscount = $discountAmount;
                        $bestPromotion = $promotion;
                    }
                }
            }

            if ($bestPromotion != null) {
                return $bestPromotion;
            }

            return false;
        }

        return false;
    }

    /**
     * get Average rating of the provider based on the service request he has entertained
     *
     * @return string
     */
    public function getAverageRating()
    {
        $rating = (new Query())
            ->select([
                'provider.id',
                'ROUND(AVG(service_request.customer_rating), 2) average'
            ])
            ->from('provider')
            ->join('INNER JOIN', 'service_request', 'provider.id=service_request.provider_id')
            ->where(['not', ['customer_rating' => null]])
            ->where(['provider.id' => $this->id])
            ->groupBy([
                'provider.id',
            ])
            ->one();

        return !empty($rating['average']) ? $rating['average'] : '-';

    }

    /**
     * Get Provider Schedule and attach it to provider
     *
     * @param $providers
     * @param $request_type
     * @return mixed
     */
    public static function getProviderSchedule($providers, $request_type)
    {
        if ($request_type === ServiceRequest::REQUEST_TYPE_COLLECTANDRETURN) {
            return $providers;
        }

        if (empty($providers)) {
            return $providers;
        }

        $providers_with_schedule = $providers;
        foreach ($providers as $id => $provider) {

            $provider = Provider::findOne(['id' => $id]);

            if (!$provider) {
                continue;
            }

            $providers_with_schedule[$id]['schedule'] = explode(':', $provider->schedule);
            $providers_with_schedule[$id]['current_week_schedule'] = explode(':', $provider->current_week_schedule);
        }

        return $providers_with_schedule;
    }

    /**
     * get coverage areas of the provider.
     * it checks for the given type and if fallback is true, and no areas are found for the given type
     * it will fetch all the areas for which type is not provided.
     *
     * @param $type
     * @param bool $fallback
     * @return array|ProviderCoveredArea[]|\yii\db\ActiveRecord[]
     */
    public function getCoverageAreas($type, $fallback = false)
    {
        // get the coverage area for the given request type
        $areas = $this->getProviderCoveredAreas()->where(['type' => $type])->all();

        // if no area found check if the areas are empty, get all the area where type is not marked.
        if (empty($areas) && $fallback) {
            $areas = $this->getProviderCoveredAreas()->where(['IS', 'type', NULL])->all();
        }

        // if only areas are marked opposite to type, get those area
        if (empty($areas) && $fallback) {
            $type = ServiceRequest::REQUEST_TYPE_INHOUSE ? ServiceRequest::REQUEST_TYPE_COLLECTANDRETURN : ServiceRequest::REQUEST_TYPE_INHOUSE;
            $areas = $this->getProviderCoveredAreas()->where(['type' => $type])->all();
        }

        return $areas;
    }

    /**
     * Check if this provider has areas marked or not.
     *
     * @return bool
     */
    public function isAreasMarked()
    {
        return count($this->getProviderCoveredAreas()->where(['IS NOT', 'type', NULL])->all()) > 0;
    }

    /**
     * Mark the type of coverage area
     * @param $type
     */
    public function markCoverageAreas($type)
    {
        $areas = $this->providerCoveredAreas;

        /** @var ProviderCoveredArea $area */
        foreach ($areas as $area) {
            $area->type = $type;
            $area->save(false);
        }
    }

    /**
     * Copy coverage area from one type to another.
     *
     * @param $from_type
     * @param $to_type
     */
    public function copyCoveredAreas($from_type, $to_type)
    {
        $areas = $this->getCoverageAreas($from_type);

        foreach ($areas as $area) {
            $newArea = new ProviderCoveredArea();
            $newArea->provider_id = $this->id;
            $newArea->coordinates = $area['coordinates'];
            $newArea->radius = $area['radius'];
            $newArea->type = $to_type;
            $newArea->save();
        }
    }

    /**
     * Check if provider has coverage of the given lat long and type
     *
     * @param $lat_long
     * @param $type
     * @return bool
     */
    public function hasCoverageOf($lat_long, $type)
    {
        // check coverage and if there is no area found for this type, get all the areas provided
        $coverage_areas = $this->getCoverageAreas($type, true);

        // check if has coverage in this area return true
        if ($this->hasCoverageIn($coverage_areas, $lat_long)) {
            return true;
        }

        // we have found the coverage areas, but there is no match
        if (!empty($coverage_areas)) {
            return false;
        }

        // now get the type that is not provided.
        $type = ServiceRequest::REQUEST_TYPE_INHOUSE === $type ? ServiceRequest::REQUEST_TYPE_COLLECTANDRETURN : ServiceRequest::REQUEST_TYPE_INHOUSE;

        // get coverage areas opposite to that type.
        $coverage_areas = $this->getCoverageAreas($type);

        // check if has coverage in these coverage areas
        if ($this->hasCoverageIn($coverage_areas, $lat_long)) {
            return true;
        }

        return false;
    }

    /**
     * check if has coverage in these areas.
     *
     * @param $areas
     * @param $lat_long
     * @return bool
     */
    public function hasCoverageIn($areas, $lat_long)
    {
        $found = false;

        foreach ($areas as $area) {
            $found = DistanceHelper::calculateDistanceInM($area->coordinates, $lat_long) <= $area->radius;

            if ($found) {
                break;
            }
        }

        return $found;
    }

    /**
     * Check if provider has enabled same day booking for specific service
     *
     * @param $id
     * @param $service_id
     * @return bool
     */
    public static function hasSameDayBooking($id, $service_id)
    {
        return 0 < (new Query())
                ->select(['provided_service.same_day_booking'])
                ->from('provider')
                ->join('INNER JOIN', 'provided_service', 'provider.id=provided_service.provider_id')
                ->join('INNER JOIN', 'service', 'provided_service.service_id=service.id')
                ->andWhere(['provider.id' => $id])
                ->andWhere(['service.id' => $service_id])
                ->andWhere(['provided_service.same_day_booking' => true])
                ->count();
    }

    /**
     * Apply Coverage area filter
     *
     * @param $currentRequest
     * @return mixed
     */
    public static function applyCoverageAreaFilter($currentRequest)
    {
        $providers = $currentRequest['providers'];
        $request_type = $currentRequest['request_type'];
        $address = $currentRequest['address'];

        if ($request_type === ServiceRequest::REQUEST_TYPE_COLLECTANDRETURN) {
            return $providers;
        }

        if (empty($providers)) {
            return $providers;
        }

        $provider_ids = Collection::make(array_keys($providers))
            ->map(function ($provider_id) {
                return Provider::findOne(['id' => $provider_id]);
            })->filter(function ($provider) {
                return $provider !== null;
            })->filter(function ($provider) use ($address) {
                /** @var Provider $provider */
                foreach ($provider->providerCoveredAreas as $coveredArea) {
                    if (DistanceHelper::calculateDistanceInM($coveredArea->coordinates, $address['coordinates']) <= $coveredArea->radius) {
                        return true;
                    }
                }
                return false;
            })->map(function ($provider) {
                return $provider->id;
            })->toArray();

        $actual = $providers;
        foreach ($providers as $id => $provider) {
            if (!in_array($id, $provider_ids)) {
                unset($providers[$id]);
            }
        }

        return $providers;
    }


    public function hasVatRegisterationDocument()
    {
        $result = false;
        if (!empty($this->vat_registeration_document_filename)
            && file_exists($this->getUploadedFilePath("vat_registeration_document_filename"))
        ) {
            $result = true;
        }
        return $result;
    }

    public function hasTradeLicenseDocument()
    {
        $result = false;
        if (!empty($this->trade_license_document_filename)
            && file_exists($this->getUploadedFilePath("trade_license_document_filename"))
        ) {
            $result = true;
        }
        return $result;
    }

    /**
     * Get Buffer time of the provided service for each provider.
     *
     * @param $service_id
     * @return int
     * @throws \Exception
     */
    public function getBufferTime($service_id)
    {
        /** @var ProvidedService $service */
        $service = $this->getProvidedServices()->where(['service_id' => $service_id])->one();

        if (!$service) {
            throw new \Exception("Service not found.");
        }

        return empty($service->hours_in_advance) ? SystemSetting::getValue("minimum_order_hours") : $service->hours_in_advance;
    }

    public function sendSuspensionEmail($reason)
    {
        if ($this->status !== static::STATUS_SUSPENDED) {
            return false;
        }

        $suspension_reason = new SuspensionReason();
        $suspension_reason->reason = $reason;
        $suspension_reason->provider_id = $this->id;
        $suspension_reason->save();

        /* @var $mail MailerInterface */
        $mail = Yii::$app->mailer->compose(['html' => 'provider-suspended'], [
            'model' => $this,
            'reason' => $reason
        ]);

        $mail->setFrom([Yii::$app->params['noReplyEmail'] => Yii::$app->params['noReplyName']]);
        $mail->setTo([$this->email]);
        $mail->setSubject('Your account has been suspended.');

        $mail->send();
    }

    /**
     * get Suspension reasons
     *
     * @return ActiveQuery
     */
    public function getSuspensionReasons()
    {
        return $this->hasMany(SuspensionReason::className(), ['provider_id' => 'id']);
    }

    public static function filterByCoverage($providers, $lat_long, $request_type)
    {
        foreach ($providers as $id => $provider) {

            /** @var static $providerModel */
            $providerModel = static::findOne($id);

            if (!$providerModel) {
                unset($providers[$id]);
                continue;
            }

            if (!$providerModel->hasCoverageOf($lat_long, $request_type)) {
                unset($providers[$id]);
            }
        }

        return $providers;
    }

    public function getGroupInfo()
    {
        return $this->hasOne(ProviderGroupMember::className(), ['provider_id' => 'id']);
    }

    public function getGroup()
    {
        return $this->hasOne(ProviderGroup::className(), ['id' => 'provider_group_id'])
            ->viaTable('provider_group_members', ['provider_id' => 'id']);
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
    public function getTeamMembers()
    {
        return $this->hasMany(TeamMember::className(), ['provider_id' => 'id']);
    }

    /**
     * @return Query
     */
    public function getTeamMembersActiveQuery()
    {
        return (new Query())
            ->select([
                'team_members.id',
                'team_members.first_name',
                'team_members.last_name',
                'team_members.gender',
                new Expression("CONCAT('" . Yii::getAlias('@uploadWeb/images/provider-team-members/file_') . "', team_members.id, '.', REPLACE(REVERSE(SUBSTRING(REVERSE(image), 1, 4)), '.', '') ) as image"),
                new Expression('ROUND(AVG(customer_rating),1) rating'),
                new Expression('count(service_request.id) service_requests'),
                'team_members.created_at',
                'team_members.updated_at',
            ])
            ->from('team_members')
            ->andWhere(['team_members.provider_id' => $this->id])
            ->join('left join', 'service_request_team_members', 'team_members.id = service_request_team_members.team_member_id')
            ->join('left join', 'service_request', 'service_request_team_members.service_request_id = service_request.id')
            ->groupBy([
                'team_members.id',
                'team_members.first_name',
                'team_members.last_name',
                'team_members.gender',
                'image',
                'team_members.created_at',
                'team_members.updated_at',
            ])->orderBy(['team_members.first_name' => SORT_ASC]);
    }

    /**
     * Get Preferred team members for this customer.
     *
     * @param $customer_id
     * @param null $selected_time
     * @param int $limit
     * @param int $rating
     * @return array
     */
    public function getPreferred($customer_id, $selected_time = null, $limit = 2, $rating = 4)
    {
        $results = [];
        $customer_query = (new Query())
            ->select(['team_members.id', new Expression('AVG(customer_rating) rating')])
            ->from('team_members')
            ->join('inner join', 'service_request_team_members', 'team_members.id = service_request_team_members.team_member_id')
            ->join('inner join', 'service_request', 'service_request_team_members.service_request_id = service_request.id')
            ->having(['>=', 'rating', $rating])
            ->orderBy(['rating' => SORT_DESC])
            ->andWhere(['team_members.status' => TeamMember::STATUS_ACTIVE])
            ->andWhere(['team_members.provider_id' => $this->id])
            ->groupBy(['team_members.id']);

        $query = clone $customer_query;

        // first check for which team member was rated 5 star by this customer
        $customer_query->andWhere(['service_request.customer_id' => $customer_id]);

        if ($customer_query->count() > 0) {
            $results = $customer_query->limit($limit)->all();
        } else {
            // if none found get team member who are rated 5 star
            $results = $query->limit($limit)->all();
        }

        if (empty($results)) {
            return [];
        }

        $team_members = [];
        foreach ($results as $result) {
            /** @var TeamMember $team_member */
            $team_member = TeamMember::findOne($result['id']);

            if (!$team_member) {
                continue;
            }

            // if selected time is not provided just get the preferred.
            if (!$selected_time) {
                $team_members[] = $team_member;
                continue;
            }

            //if selected time is given check if that team member is available for that selected time.
            if ($team_member->isAvailable($selected_time)) {
                $team_members[] = $team_member;
            }
        }

        return $team_members;
    }

    public function getGroupDetail()
    {
        if (!$this->group) {
            return [
                'group_id' => null,
                'group_name' => null,
                'members' => []
            ];
        }
        $group_id = $this->group->id;
        $group_name = $this->group->name;
        $members = Collection::make(
            $this->group
                ->getMembers()
                ->where(['!=', 'provider_id', $this->id])
                ->with('provider')
                ->all()
        )->map(function ($member) {

            /**@var ProviderGroupMember $member */
            return [
                'id' => $member->provider_id,
                'username' => $member->provider->username,
                'image' => $member->provider->account->getImage()
            ];
        });

        return [
            'group_id' => $group_id,
            'group_name' => $group_name,
            'members' => $members
        ];
    }

    public function copySchedule($copy_from, $current)
    {
        $scheduleKey = 'schedule';
        $currentWeekScheduleKey = 'current_week_schedule';

        if (!in_array($copy_from, [$scheduleKey, $currentWeekScheduleKey])) {
            return false;
        }

        if (!in_array($current, range(1, 2))) {
            return false;
        }

        $current = $current == 1 ? $currentWeekScheduleKey : $scheduleKey;

        if ($current === $copy_from) {
            return false;
        }

        $this->{$current} = $this->{$copy_from};
        $this->save();

    }

    /**
     * check if provider is available on this date time for this service.
     *
     * @param $service_id
     * @param $dateTime
     * @return bool
     */
    public function isAvailableOn($service_id, $dateTime)
    {
        $schedule = Manager::assertSchedule($this->toArray(), $dateTime);

        return Manager::isProviderAvailable($schedule, $this->getBufferTime($service_id), $dateTime, 'time');
    }

}
        
