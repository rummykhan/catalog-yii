<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace common\oldmodels;

use common\components\filters\SelectProvider;
use common\helpers\OptionsBehavior;
use common\helpers\ServiceAttributeTypeBasket;
use common\helpers\ServiceAttributeTypeMulti;
use common\helpers\ServiceAttributeTypeOption;
use common\helpers\ServiceAttributeTypeString;
use common\models\basket\BasketCellMap;
use common\models\providedservices\ProvidedService;
use common\models\providedservices\ProvidedServiceAttribute;
use common\models\providedservices\ProvidedServiceAttributeOption;
use common\models\seo\ServiceRelation;
use common\models\seo\ServiceTiplink;
use common\jobs\service\ServiceFormCache;
use common\models\users\Admin;
use common\models\servicerequests\ServiceRequest;
use common\models\settings\SystemSetting;
use common\models\users\Provider;
use omgdef\multilingual\MultilingualBehavior;
use omgdef\multilingual\MultilingualQuery;
use Yii;
use yii\base\Exception;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yiidreamteam\upload\ImageUploadBehavior;


/**
 * This is the model class for table "service".
 *
 * @author Tarek K. Ajaj
 * Apr 4, 2016 4:00:54 PM
 *
 * Service.php
 * UTF-8
 *
 * @property integer $id
 * @property integer $parent_id
 * @property string $name
 * @property string $full_name
 * @property string $image
 * @property string $icon
 * @property integer $enable
 * @property string $slug
 * @property integer $in_house
 * @property integer $collect_and_return
 * @property integer $question
 * @property integer $keywords
 * @property integer $commission_rate
 * @property boolean $can_set_minmax
 * @property string $onsite_desc
 * @property string $collectandreturn_desc
 * @property string $default_form_layout
 * @property integer $service_order
 * @property integer $sla_collect
 * @property integer $sla_complete
 * @property integer $sla_return
 * @property boolean $recurrent_allowed
 * @property boolean $switchable
 * @property boolean $can_attach_images
 * @property boolean $allow_express_order
 * @property string $express_order_description
 * @property string $mobile_icon
 * @property string $mobile_color
 * @property string $mobile_description
 * @property string $available_cities
 * @property string $description
 * @property boolean $auto_trigger_description
 * @property string $location_question
 * @property string $available_from
 * @property string $available_to
 * @property boolean $last_updated_by
 * @property string $note_on_request_title
 * @property text $note_on_request
 * @property string $discount_label
 * @property string $can_attach_images_label
 * @property boolean $must_attach_images
 * @property boolean $in_house_label
 * @property boolean $collect_and_return_label
 * @property boolean $require_minimal_address
 * @property string $instructions
 * @property string $provider_notes
 *
 *
 * @property boolean $is_deal
 * @property boolean $show_deal_in_homepage
 * @property string $deal_image
 * @property string $deal_banner_image
 *
 * @property ProvidedService[] $providedServices
 * @property ServiceCategory $parent
 * @property ServiceCategory $parentMultilingual
 * @property ServiceAttribute[] $serviceAttributes
 * @property ServiceAttribute[] $serviceAttributesMultilingual
 * @property ServiceRequest[] $serviceRequests
 * @property ServiceRelation[] $serviceRelations
 * @property ServiceTiplink[] $serviceTiplinks
 *
 * @property string $created_at
 * @property string $updated_at
 */
class Service extends ActiveRecord
{

    const STATUS_DISABLED = 0;
    const STATUS_ENABLED = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'service';
    }

    public static function getDB()
    {
        return Yii::$app->get("old");
    }

    public function rules()
    {
        return [
            [['parent_id', 'name', 'full_name'], 'required'],
            [['parent_id', 'enable', 'in_house', 'collect_and_return', 'commission_rate', 'service_order', 'last_updated_by'], 'integer'],
            [['name', 'full_name', 'question', 'default_form_layout', 'location_question', 'note_on_request_title'], 'string', 'max' => 255],
            [['discount_label'], 'string', 'max' => 20],
            [['image'], 'file', 'extensions' => 'jpg,png,jpeg', 'maxSize' => (1024 * 1024) * 4],
            [['deal_image'], 'image', 'skipOnEmpty' => false, 'extensions' => 'jpg,png,jpeg', 'maxSize' => (1024 * 1024) * 5, 'when' => function ($model) {
                return $model->is_deal == true && $model->currentdealimage == '';
            }, 'whenClient' => "function (attribute, value) {
    return false;
}"],

            ['icon', 'file', 'extensions' => 'jpg,png,svg', 'maxSize' => (1024 * 1024) * 4],
            [['keywords'], 'string'],
            [['onsite_desc', 'collectandreturn_desc', 'express_order_description'], 'string'],
            [['can_set_minmax'], 'boolean'],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => ServiceCategory::className(), 'targetAttribute' => ['parent_id' => 'id']],
            [['sla_collect', 'sla_complete', 'sla_return'], 'integer'],
            [['recurrent_allowed', 'switchable', 'can_attach_images', 'allow_express_order'], 'boolean'],
            [['service_order'], 'default', 'value' => 99],
            ['mobile_icon', 'file', 'extensions' => 'jpg,png', 'maxSize' => 512 * 1024],
            [['mobile_description', 'mobile_color'], 'string'],
            [['available_cities', 'description', 'instructions', 'provider_notes'], 'string'],
            [['auto_trigger_description'], 'boolean'],
            [['created_at', 'updated_at', 'available_from', 'available_to', 'note_on_request'], 'safe'],

            [['available_from', 'available_to'], 'validateDateFromTo'],
            [[
                'name',
                'full_name',
                'question',
                'default_form_layout',
                'location_question',
                'icon',
                'mobile_icon',
                'mobile_description',
                'mobile_color',
                'description',
                'instructions',
                'keywords',
                'onsite_desc',
                'collectandreturn_desc',
                'express_order_description',
                'available_cities'

            ], 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process'],
            [['can_attach_images_label'], 'safe'],
            [['can_attach_images_label'], 'default', 'value' => 'Attach photos.'],
            [['must_attach_images'], 'boolean'],
            [['in_house_label', 'collect_and_return_label', 'deal_banner_image', 'deal_banner_image_mobile', 'deal_image'], 'safe'],
            [['require_minimal_address', 'is_deal', 'show_deal_in_homepage'], 'boolean']
        ];
    }

    public function validateDateFromTo($attributes, $params)
    {
        if ($this->available_from && !$this->available_to)
            $this->addError('available_to', 'Available To Cannot be blank');

        if ($this->available_to && !$this->available_from)
            $this->addError('available_to', 'Available From Cannot be blank');

    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => SluggableBehavior::className(),
                'attribute' => 'name',
                // 'slugAttribute' => 'slug',
                'ensureUnique' => true,
            ],
            'status' => [
                'class' => OptionsBehavior::className(),
                'attribute' => 'enable',
                'options' => [
                    self::STATUS_DISABLED => Yii::t("app", "Disabled"),
                    self::STATUS_ENABLED => Yii::t("app", "Enabled"),
                ]
            ],
            [
                'class' => ImageUploadBehavior::className(),
                'attribute' => 'icon',
                'filePath' => '@uploadRoot/icons/service_[[pk]].[[extension]]',
                'fileUrl' => '@uploadWeb/icons/service_[[pk]].[[extension]]',
            ],
            'image' => [
                'class' => ImageUploadBehavior::className(),
                'attribute' => 'image',
                'thumbs' => [
                    'thumb' => ['width' => 750, 'height' => 450],
                ],
                'filePath' => '@uploadRoot/images/services/service_[[pk]].[[extension]]',
                'fileUrl' => '@uploadWeb/images/services/service_[[pk]].[[extension]]',
                'thumbPath' => '@uploadRoot/images/services/[[profile]]_service_[[pk]].[[extension]]',
                'thumbUrl' => '@uploadWeb/images/services/[[profile]]_service_[[pk]].[[extension]]',
            ],
            'deal_image' => [
                'class' => ImageUploadBehavior::className(),
                'attribute' => 'deal_image',
                'thumbs' => [
                    'thumb' => ['width' => 750, 'height' => 450],
                ],
                'filePath' => '@uploadRoot/images/services/deal_service_[[pk]].[[extension]]',
                'fileUrl' => '@uploadWeb/images/services/deal_service_[[pk]].[[extension]]',
                'thumbPath' => '@uploadRoot/images/services/[[profile]]_deal_service_[[pk]].[[extension]]',
                'thumbUrl' => '@uploadWeb/images/services/[[profile]]_deal_service_[[pk]].[[extension]]',
            ],
            'deal_banner_image' => [
                'class' => ImageUploadBehavior::className(),
                'attribute' => 'deal_banner_image',
                'thumbs' => [
                    'thumb' => ['width' => 750, 'height' => 450],
                ],
                'filePath' => '@uploadRoot/images/services/deal_banner_service_[[pk]].[[extension]]',
                'fileUrl' => '@uploadWeb/images/services/deal_banner_service_[[pk]].[[extension]]',
                'thumbPath' => '@uploadRoot/images/services/[[profile]]_deal_banner_service_[[pk]].[[extension]]',
                'thumbUrl' => '@uploadWeb/images/services/[[profile]]_deal_banner_service_[[pk]].[[extension]]',
            ],
            'deal_banner_image_mobile' => [
                'class' => ImageUploadBehavior::className(),
                'attribute' => 'deal_banner_image_mobile',
                'thumbs' => [
                    'thumb' => ['width' => 750, 'height' => 450],
                ],
                'filePath' => '@uploadRoot/images/services/deal_banner_service_mobile_[[pk]].[[extension]]',
                'fileUrl' => '@uploadWeb/images/services/deal_banner_service_mobile_[[pk]].[[extension]]',
                'thumbPath' => '@uploadRoot/images/services/[[profile]]_deal_banner_service_mobile_[[pk]].[[extension]]',
                'thumbUrl' => '@uploadWeb/images/services/[[profile]]_deal_banner_service_mobile_[[pk]].[[extension]]',
            ],
            'mobile_icon' => [
                'class' => ImageUploadBehavior::className(),
                'attribute' => 'mobile_icon',
                'thumbs' => [
                    'thumb' => ['width' => 128, 'height' => 128],
                ],
                'filePath' => '@uploadRoot/images/services/service_icon_[[pk]].[[extension]]',
                'fileUrl' => '@uploadWeb/images/services/service_icon_[[pk]].[[extension]]',
                'thumbPath' => '@uploadRoot/images/services/[[profile]]_service_icon_[[pk]].[[extension]]',
                'thumbUrl' => '@uploadWeb/images/services/[[profile]]_service_icon_[[pk]].[[extension]]',
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
     *
     * @return MultilingualQuery
     */
    public static function find()
    {
        return new MultilingualQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'id' => Yii::t('app/services', 'ID'),
            'parent_id' => Yii::t('app/services', 'Parent Category'),
            'name' => Yii::t('app/services', 'Name'),
            'image' => Yii::t('app/services', 'Image'),
            'enable' => Yii::t('app/services', 'Status'),
            'enable_label' => Yii::t('app/services', 'Status'),

            'in_house' => !empty($this->in_house_label) ? $this->in_house_label : Yii::t('app/services', 'Onsite'),
            'collect_and_return' => !empty($this->collect_and_return_label) ? $this->collect_and_return_label : Yii::t('app/services', 'Collect & Return'),

            'collectandreturn_desc' => 'Overwrite Collect & return description',
            'onsite_desc' => 'Overwrite Onsite Description',
            'can_set_minmax' => Yii::t('app/services', 'Provider can set min/max accepted qty for this service?'),
            'sla_collect' => Yii::t('app/services', 'SLA Collection'),
            'sla_complete' => Yii::t('app/services', 'SLA Completion'),
            'sla_return' => Yii::t('app/services', 'SLA Return'),
            'recurrent_allowed' => Yii::t('app/services', 'Recurrent Allowed (Onsite only)'),
            'allow_express_order' => Yii::t('app/services', 'Allow one click order?'),
            'express_order_description' => Yii::t('app/services', 'One click order description'),
            'mobile_description' => Yii::t('app/services', 'Short description'),
            'service_order' => Yii::t('app', 'Service Order'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'note_on_request_title' => Yii::t('app', 'Note Title'),
            'note_on_request' => Yii::t('app', 'New Request Note'),
            'can_attach_images_label' => Yii::t('app', 'Question for images attachment'),
            'must_attach_images' => Yii::t('app', 'Attachments are required?'),
            'default_can_attach_images_label' => Yii::t('app', 'Attach photos'),
            'provider_notes' => Yii::t('app', 'Provider Notes'),
            'is_deal' => Yii::t('app', 'Include in Deals'),
            'deal_image' => Yii::t('app', 'Upload Deals image'),

            'show_deal_in_homepage' => Yii::t('app', 'Visible on Homepage'),
            'deal_banner_image' => Yii::t('app', 'Upload Banner Image - Desktop'),
            'deal_banner_image_mobile' => Yii::t('app', 'Upload Banner Image - Mobile'),
        ]);
    }

    public function attributeHints()
    {
        return [
            'location_question' => Yii::t("app", 'Question that appears on the "Select your Location" page')
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getProvidedServices()
    {
        return $this->hasMany(ProvidedService::className(), ['service_id' => 'id']);
    }

    public function getLastupdatedby()
    {
        return $this->hasOne(Admin::className(), ['id' => 'last_updated_by']);
    }

    /**
     * @return ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(ServiceCategory::className(), ['id' => 'parent_id']);
    }

    /**
     * @return note_on_request
     */
    public function getNoteOnRequest()
    {
        if (!empty($this->note_on_request)) {
            $note_on_request = [];
            $note_on_request['title'] = $this->note_on_request_title;
            $note_on_request['text'] = $this->note_on_request;
            return $note_on_request;
        } else if ($this->getParent()) {
            return $this->parent->noteOnRequest;
        }
        return array();
    }

    /**
     * @return ActiveQuery
     */
    public function getParentMultilingual()
    {
        return $this->getParent()->multilingual();
    }

    /**
     * @return ActiveQuery
     */
    public function getServiceAttributes()
    {
        return $this->hasMany(ServiceAttribute::className(), ['service_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getServiceAttributesMultilingual()
    {
        return $this->getServiceAttributes()->multilingual();
    }

    /**
     * @return ActiveQuery
     */
    public function getServiceRequests()
    {
        return $this->hasMany(ServiceRequest::className(), ['service_id' => 'id']);
    }

    /**
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->parent->name . ' / ' . $this->name;
    }

    /**
     *
     * @param string $slug
     * @return Service
     */
    public static function findBySlug($slug)
    {
        return static::findOne(["slug" => $slug, 'enable' => self::STATUS_ENABLED]);
    }

    /**
     * Get list of providers providing this service based on input attributes values
     *
     * @param $serviceAttributesAndOptionsOriginal
     * @param $request_type
     * @param bool $return_provider_ids
     * @param bool $filter_sensitive_data
     * @param bool $country
     * @param null $lat_long
     * @return array
     */
    public function getMatchingProviders($serviceAttributesAndOptionsOriginal, $request_type, $return_provider_ids = true, $filter_sensitive_data = false, $country = false, $lat_long = null)
    {
        $serviceAttributesAndOptions = [];
        $basketOptionsQuantity = [];
        $freeTextAttributes = [];
        $optionalAttributes = [];
        $totalItemsCount = 0;
        foreach ($serviceAttributesAndOptionsOriginal as $attr_key => $attrandopt) {
            $serviceAttr = ServiceAttribute::findOne($attr_key);
            if ($serviceAttr->type == ServiceAttributeTypeBasket::getKey()) {
                if (empty($attrandopt)) {
                    continue;
                }
                $basketItems = Json::decode($attrandopt);
                $basketVals = [];
                foreach ($basketItems as $key => $basketItem) {
                    foreach ($basketItem[1] as $srkey => $basServ) {

                        $cellMap = BasketCellMap::find()
                            ->where(['basket_item_id' => $basketItem[0]])
                            ->andWhere(['basket_service_id' => $basServ])
                            ->one();
                        $basketVals[] = $cellMap->service_attribute_option_id;
                        $basketOptionsQuantity[$cellMap->service_attribute_option_id] = $basketItem[2];
                        $totalItemsCount += $basketItem[2];
                    }
                }
                $serviceAttributesAndOptions[$attr_key] = $basketVals;
            } else {
                $serviceAttributesAndOptions[$attr_key] = $attrandopt;
                if ($serviceAttr->type == ServiceAttributeTypeString::getKey()) {
                    $freeTextAttributes[$attr_key] = $attr_key;
                }
            }
            if ($serviceAttr->is_optional) {
                $optionalAttributes[$attr_key] = $attr_key;
            }
        }
        /**
         * remove all inputed attribute that are not of the type options
         * they don't affect the price nor the provider matching
         */
        $selectedAttributesIds = array_keys($serviceAttributesAndOptions);
        $optionableAttributes = ServiceAttribute::find()
            ->select(["id"])
            ->where(["id" => $selectedAttributesIds, "type" => [
                ServiceAttributeTypeOption::getKey(),
                ServiceAttributeTypeMulti::getKey(),
                ServiceAttributeTypeBasket::getKey(),
                ServiceAttributeTypeString::getKey(),
            ]])
            ->asArray()
            ->all();
        $optionableAttributesIds = ArrayHelper::map($optionableAttributes, "id", "id");
        $serviceAttributesAndOptions = array_intersect_key($serviceAttributesAndOptions, $optionableAttributesIds);

        $options = [];
        foreach ($serviceAttributesAndOptions as $key => $attrOpt) {
            if (is_array($attrOpt)) {
                $options = array_merge($options, $attrOpt);
            } else {
                if (array_key_exists($key, $freeTextAttributes)) {
                    $setviceattropt = ServiceAttributeOption::findOne(['service_attribute_id' => $key]);
                    $options[] = $setviceattropt->id;
                } else {
                    if (array_key_exists($key, $optionalAttributes)) {
                        if (!empty($attrOpt)) {
                            $options[] = $attrOpt;
                        }
                    } else {
                        $options[] = $attrOpt;
                    }
                }
            }
        }
        //print_r($options);exit();
        $service = '';
        if (!empty($options) && isset($options) && is_array($options)) {
            $findOne = ServiceAttributeOption::findOne($options[0]);
            $service = $findOne->serviceAttribute->service;
        }

        $providerWithCost = ProvidedServiceAttributeOption::find()
            ->select(["provider_id", "count(*) as count, sum(cost) as cost"])
            ->where(["attribute_option_id" => $options])
            ->groupBy("provider_id")
            ->asArray()
            ->having("count = " . count($options))
            ->all();

        foreach ($providerWithCost as $key => $pro) {
            $providerWithCost[$key]['cost'] = 0;
            foreach ($options as $optkey => $option) {
                /* @var $psao ProvidedServiceAttributeOption */
                $psao = ProvidedServiceAttributeOption::find()
                    ->where(['attribute_option_id' => $option])
                    ->andWhere(['provider_id' => $pro['provider_id']])
                    ->one();


                if (key_exists($psao->attribute_option_id, $basketOptionsQuantity)) {
                    $providerWithCost[$key]['cost'] += $psao->cost * $basketOptionsQuantity[$psao->attribute_option_id];
                } else {
                    $providerWithCost[$key]['cost'] += $psao->cost;
                }
            }
        }

        $providers = [];

        foreach ($providerWithCost as $key => $pr) {
            $provider = Provider::findOne($pr['provider_id']);

            if ($provider->status != Provider::STATUS_ACTIVE)
                continue;

            // if lat_long is not null then check coverage of provider with that latlong and if provider has no coverage of
            // continue, don't add that provider to the mix
            if (!is_null($lat_long) && !$provider->hasCoverageOf($lat_long, $request_type)) {
                continue;
            }

            if ($country) {
                if ($provider->country != $country)
                    continue;
            }

            /* @var $providedService ProvidedService */
            $providedService = $provider->getProvidedServices()
                ->multilingual()
                ->where(['service_id' => $service->id])
                ->andWhere(['enable' => ProvidedService::STATUS_ACTIVE])
                ->andWhere(["$request_type" => 1])
                ->one();

            if (!isset($providedService) || empty($providedService))
                continue;

            if (isset($providedService->min_accepted) && $totalItemsCount < $providedService->min_accepted)
                continue;

            if (isset($providedService->max_accepted) && $totalItemsCount > $providedService->max_accepted)
                continue;


            $pr['cost'] += $providedService->{$request_type . '_price'};

            $requestTypeCharge = $providedService->{$request_type . '_price'};

            if ($providedService->cost > 0) {
                $pr['cost'] = $providedService->cost;
                $requestTypeCharge = 0;
            }

            if ($filter_sensitive_data) {
                unset($provider->password);
                unset($provider->email);
                unset($provider->password_reset_token);
                unset($provider->auth_key);
                unset($provider->firstname);
                unset($provider->lastname);
                unset($provider->birthdate);
                unset($provider->phone);
                unset($provider->building);
                unset($provider->building_number);
                unset($provider->street);
                unset($provider->postcode);
                unset($provider->locality);
                unset($provider->city);
                unset($provider->region);
                unset($provider->created_at);
                unset($provider->updated_at);
            }

            // schedule assertion
            $schedule = $provider->schedule;
            if (empty($schedule)) {
                $schedule = $provider->current_week_schedule;
            }

            if ((((double)$pr['cost']) < 0) || empty($schedule)) {
                continue;
            }

            $current_week_schedule = empty($provider->current_week_schedule) ? 'x' : $provider->current_week_schedule;
            $provider->current_week_schedule = $current_week_schedule;

            // add every thing to an array
            if ($return_provider_ids)
                $providers[$provider->id]['provider'] = $provider->id;
            else {
                $providers[$provider->id]['provider'] = $provider;
            }

            if ($pr['cost'] < $providedService->minimum_cost) {
                $pr['cost'] = $providedService->minimum_cost;
                $requestTypeCharge = 0;
            }
            $providers[$provider->id]['username'] = $provider->username;
            $providers[$provider->id]["min_cost"] = $providedService->minimum_cost;
            $providers[$provider->id]["min_cost_formated"] = Yii::$app->getFormatter()->asCurrency($providedService->minimum_cost, $provider->currency);

            $providers[$provider->id]['rating'] = $provider->getAvgCategoryRating($service->id);
            $providers[$provider->id]['seller_note'] = $provider->seller_note;
            $providers[$provider->id]['badges'] = $provider->badges;
            $providers[$provider->id]['badges_count'] = count($provider->badges);
            $providers[$provider->id]["cost"] = $pr['cost'];
            $providers[$provider->id]["cost_formated"] = Yii::$app->getFormatter()->asCurrency($pr['cost'], $provider->currency);
            $providers[$provider->id]["request_type_charge"] = $requestTypeCharge;
            $providers[$provider->id]["can_provider_use_cc"] = $provider->canUseCreditCard();
            $providers[$provider->id]["currency"] = $provider->currency;
            $providers[$provider->id]["schedule"] = $provider->schedule;
            $providers[$provider->id]["current_week_schedule"] = $provider->current_week_schedule;
            $providers[$provider->id]["buffer_time"] = $providedService->getBufferTime();
            $providers[$provider->id]["provider_notes"] = $providedService->provider_notes;
            $providers[$provider->id]["provider_notes_ar"] = $providedService->provider_notes_ar;

        }

        return $providers;

        echo "<pre>";
        print_r($providers);
        echo "</pre>";
        exit();

        // <editor-fold defaultstate="collapsed" desc="Old">
        //#############################################################
        //#############################################################
        //####################All The Bellow Are Absolute##############
        //####################And No Longer Used At All################
        //#############################################################
        //#############################################################
        /**
         * Get ids of options values the customer selected
         * then get related provided attributes ids, providers has set they provide
         * those option in
         */
        $selectedOptions = array_values($serviceAttributesAndOptions);
        $providedOptions = ProvidedServiceAttributeOption::find()
            ->select(["provided_service_attribute_id"])
            ->where(["attribute_option_id" => $selectedOptions])
            ->asArray()
            ->all();

        /**
         * from the list of provided attributes select their parent provided services
         * where all the selected services are provided in
         *
         * e.g. if customer selected he want iphone repair black 32gb brokenscreen
         * we get the list of provided services where providers has set they provide
         * those options in
         */
        $providedAttributesIds = ArrayHelper::getColumn($providedOptions, 'provided_service_attribute_id');
        $providedServices = ProvidedServiceAttribute::find()
            ->select(["provided_service_id", "count(*) as count"])
            ->where(["id" => $providedAttributesIds])
            ->groupBy("provided_service_id")
            ->having("count = " . count($selectedOptions))
            ->asArray()
            ->all();

        /**
         * then from the list of provided services we can query the list of
         * providers who provide those services
         */
        $providedServicesIds = ArrayHelper::getColumn($providedServices, 'provided_service_id');
        $providersList = ProvidedService::find()
            ->select("provider_id")
            ->where(["id" => $providedServicesIds])
            ->andWhere(["$request_type" => 1])
            ->asArray()
            ->all();

        $providersIds = ArrayHelper::getColumn($providersList, 'provider_id');
        /* @var $providers Provider */
        $providers = Provider::find()
            ->where(["id" => $providersIds])
            ->andWhere(["status" => Provider::STATUS_ACTIVE])
            ->all();

        /**
         * After querying the list of providers we need to get the total cost of
         * the options the client selected for each provider and put all that in
         * a response array
         */
        $result = [];
        foreach ($providers as $key => $provider) { // for each provider in list
            $cost = 0;
            $providedService = ProvidedService::findOne([ // get the related providedService
                "service_id" => $this->id,
                "provider_id" => $provider->id
            ]);
            $cost += $providedService->getCost(); // if has a fixed cost add it to total
            $cost += $providedService->{$request_type . '_price'}; // if has a fixed cost add it to total

            foreach ($serviceAttributesAndOptions as $attrid => $optid) { // for each selected option by the client
                $providedServiceAttribute = ProvidedServiceAttribute::findOne([ // get the related provided attribute by the provider
                    "provided_service_id" => $providedService->id,
                    "service_attribute_id" => $attrid
                ]);
                $cost += $providedServiceAttribute->getCost(); // if has set a fixed price add it to the total

                $providedServiceAttributeOption = ProvidedServiceAttributeOption::findOne([ // get the related option value for the attribute
                    "provided_service_attribute_id" => $providedServiceAttribute->id,
                    "attribute_option_id" => $optid
                ]);
                $cost += $providedServiceAttributeOption->getCost(); // if has a price add to the total
            }


            if ($filter_sensitive_data) {
                unset($provider->password);
                unset($provider->email);
                unset($provider->password_reset_token);
                unset($provider->auth_key);
                unset($provider->firstname);
                unset($provider->lastname);
                unset($provider->birthdate);
                unset($provider->phone);
                unset($provider->building);
                unset($provider->building_number);
                unset($provider->street);
                unset($provider->postcode);
                unset($provider->locality);
                unset($provider->city);
                unset($provider->region);
                unset($provider->created_at);
                unset($provider->updated_at);
            }

            // add every thing to an array
            if ($return_provider_ids) {
                $result[$provider->id]['provider'] = $provider->id;
            } else {
                $result[$provider->id]['provider'] = $provider;
            }
            $result[$provider->id]["cost"] = $cost;
        }

        // return the array
        return $result;
        // </editor-fold>
    }

    public function resetProviders($current_request, $considerLocation = false)
    {
        $providers = [];
        if (isset($current_request['is_express']) && $current_request['is_express'] == true) {
            $providers = $this->getMatchingExpressProviders($current_request['service'], $current_request['request_type'], true, false, Yii::$app->cityselect->getCountry(), null);

        } else {
            $providers = $this->getMatchingProviders($current_request['attributes'], $current_request['request_type'], true, false, Yii::$app->cityselect->getCountry(), null);

        }
        $locationId = $current_request['locationid'];
        $filteredProviders = [];
        if ($considerLocation && isset($current_request['locations'][$locationId]['coord'])) {
            $coord = $current_request['locations'][$locationId]['coord'];
            foreach ($providers as $id => $provider) {
                $providerModel = Provider::findOne($id);
                if ($providerModel->hasCoverageOf($coord, $current_request['request_type'])) {
                    $filteredProviders[$id] = $provider;
                }
            }
            return $filteredProviders;
        }
        return $providers;
    }


    public function getMatchingExpressProviders($service_id, $return_provider_ids = true, $filter_sensitive_data = false, $country = false)
    {
        /* @var $providedServices ProvidedService[] */
        $providedServices = ProvidedService::find()
            ->with(['provider'])
            ->multilingual()
            ->where([
                'AND',
                ['service_id' => $service_id],
                ['enable' => ProvidedService::STATUS_ACTIVE],
                ['express_order' => 1],
                ['collect_and_return' => 1],
                ['>', 'minimum_cost', 0],
            ])
            ->all();
        $providers = [];
        if (isset($providedServices) && !empty($providedServices) && is_array($providedServices)) {
            foreach ($providedServices as $key => $providedService) {
                $provider = $providedService->provider;

                if ($provider->status != Provider::STATUS_ACTIVE)
                    continue;

                if ($country) {
                    if ($provider->country != $country)
                        continue;
                }

                $cost = 0;

                if (isset($providedService->minimum_cost) && $providedService->minimum_cost != null) {
                    $cost += $providedService->minimum_cost;
                }
                if (isset($providedService->express_order_charge) && $providedService->express_order_charge != null) {
                    $cost += $providedService->express_order_charge;
                }
                // add every thing to an array
                if ($return_provider_ids) {
                    $providers[$provider->id]['provider'] = $provider->id;
                } else {

                    if ($filter_sensitive_data) {
                        unset($provider->password);
                        unset($provider->email);
                        unset($provider->password_reset_token);
                        unset($provider->auth_key);
                        unset($provider->firstname);
                        unset($provider->lastname);
                        unset($provider->birthdate);
                        unset($provider->phone);
                        unset($provider->building);
                        unset($provider->building_number);
                        unset($provider->street);
                        unset($provider->postcode);
                        unset($provider->locality);
                        unset($provider->city);
                        unset($provider->region);
                        unset($provider->created_at);
                        unset($provider->updated_at);
                    }
                    $providers[$provider->id]['provider'] = $provider;
                }
                $providers[$provider->id]["min_cost"] = $providedService->minimum_cost;
                $providers[$provider->id]["cost"] = $cost;
                $providers[$provider->id]["request_type_charge"] = 0;
                $providers[$provider->id]["can_provider_use_cc"] = $provider->canUseCreditCard();
                $providers[$provider->id]['username'] = $provider->username;
                $providers[$provider->id]['rating'] = $provider->getAvgCategoryRating($service_id);
                $providers[$provider->id]['seller_note'] = strip_tags($provider->seller_note);
                $providers[$provider->id]['badges'] = $provider->badges;
                $providers[$provider->id]["currency"] = $provider->currency;
                $providers[$provider->id]["provider_notes"] = strip_tags($providedService->provider_notes);
                $providers[$provider->id]["provider_notes_ar"] = strip_tags($providedService->provider_notes_ar);
            }
        }

        return $providers;
        echo "<pre>";
        print_r($providers);
        echo "</pre>";
        exit();
    }


    /**
     *
     * @return ServiceCategory
     */
    public function getTopCategory()
    {
        $parent = $this->parent;
        while ($parent->parent_id != null) {
            $parent = $parent->parent;
        }
        return $parent;
    }

    public function getTopSubCategory()
    {
        $parent = $this->parent;
        $sub = $this->parent;
        while ($parent->parent_id != null) {
            $sub = $parent;
            $parent = $parent->parent;
        }
        return $sub;
    }

    public function getTopCategoryOptimized()
    {
        $parent_id = $this->getParent()->select(['parent_id'])->asArray()->one()['parent_id'];
        if (isset($parent_id) && $parent_id != null) {
            while (($temp_id = ServiceCategory::find()->select(['parent_id'])->where(['id' => $parent_id])->asArray()->one()['parent_id']) != null) {
                $parent_id = $temp_id;
            }
            return ServiceCategory::findOne($parent_id);
        } else {
            return $this->parent;
        }
    }

    public function getTopSubCategoryOptimized()
    {
        $parent_id = $this->getParent()->select(['parent_id'])->asArray()->one()['parent_id'];
        $sub_id = $parent_id;
        if (isset($parent_id) && $parent_id != null) {
            while (($temp_id = ServiceCategory::find()->select(['parent_id'])->where(['id' => $parent_id])->asArray()->one()['parent_id']) != null) {
                $sub_id = $parent_id;
                $parent_id = $temp_id;
            }
            return ServiceCategory::findOne($sub_id);
        } else {
            return $this->parent;
        }
    }

    public function getCommissionRate()
    {
        if (isset($this->commission_rate)) {
            return $this->commission_rate;
        }
        return $this->parent->getCommissionRate();
    }

    public function getCommissionRateText($full = false)
    {
        if (isset($this->commission_rate)) {
            if ($full) {
                return "$this->commission_rate% ($this->full_name)";
            } else {
                return "$this->commission_rate%";
            }
        }
        return $this->parent->getCommissionRateText(true);
    }

    public function getSLACollect()
    {
        if (isset($this->sla_collect)) {
            return $this->sla_collect;
        }
        return $this->parent->getSLACollect();
    }

    public function getSLAComplete()
    {
        if (isset($this->sla_complete)) {
            return $this->sla_complete;
        }
        return $this->parent->getSLAComplete();
    }

    public function getSLAReturn()
    {
        if (isset($this->sla_return)) {
            return $this->sla_return;
        }
        return $this->parent->getSLAReturn();
    }

    /**
     * return the path to the icon representing this service or its category
     * @return string
     */
    public function getIconPath()
    {
        if (isset($this->icon) && file_exists($this->getUploadedFilePath('icon'))) {
            return $this->getUploadedFilePath('icon');
        }
        if ($this->parent_id != null) {
            return $this->parent->getIconPath(true);
        }
        return Yii::getAlias("@staticRoot") . DIRECTORY_SEPARATOR . "default" . DIRECTORY_SEPARATOR . 'icon.svg';
    }

    /**
     * @return ActiveQuery
     */
    public function getServiceRelations()
    {
        return $this->hasMany(ServiceRelation::className(), ['service_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getServiceTiplinks()
    {
        return $this->hasMany(ServiceTiplink::className(), ['service_id' => 'id']);
    }

    public function isPromotionAllowed()
    {

        $serviceIds = explode(",", SystemSetting::getValue("no_promotion_services"));

        if (empty($serviceIds))
            return true;

        if (in_array($this->id, $serviceIds))
            return false;

        $categoriesIds = explode(",", SystemSetting::getValue("no_promotion_categories"));
        $categoriesIds = array_filter($categoriesIds);

        if (empty($categoriesIds))
            return true;

        $subServicesIds = [];
        foreach ($categoriesIds as $key => $categoryId) {
            if (!empty($categoryId)) {
                $subServicesIds = array_merge($subServicesIds, ServiceCategory::getSubServicesIds($categoryId));
            }
        }

        $subServicesIds = array_unique($subServicesIds);

        if (in_array($this->id, $subServicesIds))
            return false;

        return true;
    }

    public function updateCatalog()
    {
        if ($this->parent->is_visible) {
            SystemSetting::increment("catalog_version");
        }
    }

    public function cacheServiceForm()
    {
        Yii::$app->serviceQueue->push(new ServiceFormCache([
            'serviceId' => $this->id
        ]));
        Yii::$app->serviceQueue->push(new ServiceFormCache([
            'serviceId' => $this->id,
            'language' => 'ar'
        ]));
    }

    public function getParentsList()
    {
        $parents = [];
        $current = $this;
        $parents[] = $current->name;
        while ($current = $current->parent) {
            $parents[] = $current->name;
        }
        return $parents;
    }

    public function getType()
    {
        return 'service';
    }

    public function getCurrentdealimage()
    {
        $model = static::findOne($this->id);
        return $model->getImageFileUrl('deal_image');
    }

    public function getCurrentdealbannerimage()
    {
        $model = static::findOne($this->id);
        return $model->getImageFileUrl('deal_banner_image');
    }

    public function getCurrentdealbannerimagemobile()
    {
        $model = static::findOne($this->id);
        return $model->getImageFileUrl('deal_banner_image_mobile');
    }

    public function belongsToCategories($categoryIds)
    {
        if (empty($categoryIds)) {
            return false;
        }

        $query = (new Query())
            ->select([
                'service.id',
            ])
            ->from('service')
            ->join('left join', 'service_category level1', 'service.parent_id=level1.id')
            ->join('left join', 'service_category level2', 'level1.parent_id=level2.id')
            ->join('left join', 'service_category level3', 'level2.parent_id=level3.id')
            ->join('left join', 'service_category level4', 'level3.parent_id=level4.id')
            ->where([
                'OR',
                ['IN', 'level1.id', $categoryIds],
                ['IN', 'level2.id', $categoryIds],
                ['IN', 'level3.id', $categoryIds],
                ['IN', 'level4.id', $categoryIds],
            ])
            ->andWhere(['service.id' => $this->id])
            ->distinct();

        return count($query->all()) > 0;

    }
}

