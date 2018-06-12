<?php

namespace common\oldmodels;

use common\helpers\arr\Collection;
use common\helpers\OptionsBehavior;
use common\models\ServiceCategorySlider;
use common\models\settings\SystemSetting;
use common\models\users\Admin;
use omgdef\multilingual\MultilingualBehavior;
use omgdef\multilingual\MultilingualQuery;
use yii\behaviors\TimestampBehavior;
use Yii;
use yii\behaviors\SluggableBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yiidreamteam\upload\ImageUploadBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "service_category".
 *
 * @author Tarek K. Ajaj
 * Apr 4, 2016 3:55:23 PM
 *
 * ServiceCategory.php
 * UTF-8
 *
 * @property integer $id
 * @property integer $parent_id
 * @property string $name
 * @property integer $ui_layout
 * @property string $image
 * @property integer $enable
 * @property string $slug
 * @property string $icon
 * @property string $icon_path
 * @property string $shortname
 * @property string $question
 * @property integer $commission_rate
 * @property integer $category_order
 * @property string $footer_message
 * @property integer $sla_collect
 * @property integer $sla_complete
 * @property integer $sla_return
 * @property boolean $use_icon_for_childs
 * @property string $mobile_icon
 * @property string $mobile_color
 * @property string $mobile_description
 * @property boolean $is_service_on_mobile
 * @property string $available_cities
 * @property string $description
 * @property boolean $auto_trigger_description
 * @property boolean $last_updated_by
 * @property string $note_on_request_title
 * @property text $note_on_request
 * @property string $discount_label
 * @property string $wingae_size
 * @property string $sub_ui_style
 * @property string $deal_image
 * @property boolean $is_deal
 *
 * @property Service[] $services
 * @property ServiceCategory $parent
 * @property ServiceCategory[] $serviceCategories sub-categories
 * @property ServiceCategorySlider[] $sliders
 * @property boolean $is_visible
 *
 * @property string $created_at
 * @property string $updated_at
 */
class ServiceCategory extends ActiveRecord
{

    const STATUS_DISABLED = 0;
    const STATUS_ENABLED = 1;
    const UI_ICON_TEXT = 'icon-and-text';
    const UI_ICON_ONLY = 'icon-only';
    const UI_TEXT_ONLY = 'text-only';

    // wingae_sizes
    const SIZE_SMALL = 'small';
    const SIZE_MEDIUM = 'medium';
    const SIZE_LARGE = 'large';
    const SIZE_EXTRA_LARGE = 'extra_large';

    const SUB_UI_STYLE_PAGES = 'pages';
    const SUB_UI_STYLE_DROPDOWN = 'dropdown';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'service_category';
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
            [['parent_id', 'enable', 'commission_rate', 'category_order', 'last_updated_by'], 'integer'],
            [['name', 'shortname', 'question',], 'required'],
            [['name', 'shortname', 'question', 'ui_layout', 'note_on_request_title'], 'string', 'max' => 255],
            [['name', 'shortname', 'question', 'ui_layout'], 'string', 'max' => 255],
            [['discount_label'], 'string', 'max' => 20],
            [['image'], 'file', 'extensions' => 'jpg,png,jpeg', 'maxSize' => (1024 * 1024) * 4],
            [['footer_message'], 'string'],
            [['icon', 'icon_path', 'note_on_request'], 'safe'],
            ['icon', 'file', 'extensions' => 'jpg,png,svg', 'maxSize' => (1024 * 1024) * 4],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => ServiceCategory::className(), 'targetAttribute' => ['parent_id' => 'id']],
            [['sla_collect', 'sla_complete', 'sla_return'], 'integer'],
            [['use_icon_for_childs'], 'boolean'],
            ['mobile_icon', 'file', 'extensions' => 'jpg,png', 'maxSize' => 512 * 1024],
            [['mobile_description', 'mobile_color'], 'string'],
            [['is_service_on_mobile'], 'boolean'],
            [['available_cities', 'description', 'sub_ui_style'], 'string'],
            [['sub_ui_style'], 'default', 'value' => self::SUB_UI_STYLE_PAGES],
            [['auto_trigger_description', 'is_visible','is_deal'], 'boolean'],
            [['created_at', 'updated_at','deal_image'], 'safe'],
            [[
                'name',
                'shortname',
                'question',
                'image',
                'footer_message',
                'icon',
                'icon_path',
                'mobile_icon',
                'mobile_description',
                'mobile_color',
                'available_cities'

            ], 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process'],
            [['wingae_size'], 'in', 'range' => [static::SIZE_SMALL, static::SIZE_MEDIUM, static::SIZE_LARGE, static::SIZE_EXTRA_LARGE]]
        ];
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
                    self::STATUS_DISABLED => Yii::t("app", 'Disabled'),
                    self::STATUS_ENABLED => Yii::t("app", 'Enabled'),
                ]
            ],
            'ui_layout' => [
                'class' => OptionsBehavior::className(),
                'attribute' => 'ui_layout',
                'options' => [
                    self::UI_ICON_TEXT => 'Icon & Text',
                    self::UI_ICON_ONLY => 'Icon Only',
                    self::UI_TEXT_ONLY => 'Text Only',
                ]
            ],
            'sub_ui_style' => [
                'class' => OptionsBehavior::className(),
                'attribute' => 'sub_ui_style',
                'options' => [
                    self::SUB_UI_STYLE_PAGES => 'Pages',
                    self::SUB_UI_STYLE_DROPDOWN => 'Dropdown'
                ]
            ],
            [
                'class' => ImageUploadBehavior::className(),
                'attribute' => 'icon',
                'filePath' => '@uploadRoot/icons/icon_[[pk]].[[extension]]',
                'fileUrl' => '@uploadWeb/icons/icon_[[pk]].[[extension]]',
            ],
            'image' => [
                'class' => ImageUploadBehavior::className(),
                'attribute' => 'image',
                'thumbs' => [
                    'thumb' => ['width' => 750, 'height' => 450],
                ],
                'filePath' => '@uploadRoot/images/servicecategories/servicecategory_[[pk]].[[extension]]',
                'fileUrl' => '@uploadWeb/images/servicecategories/servicecategory_[[pk]].[[extension]]',
                'thumbPath' => '@uploadRoot/images/servicecategories/[[profile]]_servicecategory_[[pk]].[[extension]]',
                'thumbUrl' => '@uploadWeb/images/servicecategories/[[profile]]_servicecategory_[[pk]].[[extension]]',
            ],
            'deal_image' => [
                'class' => ImageUploadBehavior::className(),
                'attribute' => 'deal_image',
                'thumbs' => [
                    'thumb' => ['width' => 750, 'height' => 450],
                ],
                'filePath' => '@uploadRoot/images/servicecategories/deal_servicecategory_[[pk]].[[extension]]',
                'fileUrl' => '@uploadWeb/images/servicecategories/deal_servicecategory_[[pk]].[[extension]]',
                'thumbPath' => '@uploadRoot/images/servicecategories/[[profile]]_deal_servicecategory_[[pk]].[[extension]]',
                'thumbUrl' => '@uploadWeb/images/servicecategories/[[profile]]_deal_servicecategory_[[pk]].[[extension]]',
            ],
            'mobile_icon' => [
                'class' => ImageUploadBehavior::className(),
                'attribute' => 'mobile_icon',
                'thumbs' => [
                    'thumb' => ['width' => 128, 'height' => 128],
                ],
//                'filePath' => '@uploadRoot/images/services/service_icon_[[pk]].[[extension]]',
//                'fileUrl' => '@uploadWeb/images/services/service_icon_[[pk]].[[extension]]',
//                'thumbPath' => '@uploadRoot/images/services/[[profile]]_service_icon_[[pk]].[[extension]]',
//                'thumbUrl' => '@uploadWeb/images/services/[[profile]]_service_icon_[[pk]].[[extension]]',
                'filePath' => '@uploadRoot/images/categories/service_icon_[[pk]].[[extension]]',
                'fileUrl' => '@uploadWeb/images/categories/service_icon_[[pk]].[[extension]]?_=' . strtotime($this->updated_at),
                'thumbPath' => '@uploadRoot/images/categories/[[profile]]_service_icon_[[pk]].[[extension]]',
                'thumbUrl' => '@uploadWeb/images/categories/[[profile]]_service_icon_[[pk]].[[extension]]?_=' . strtotime($this->updated_at),
            ],
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ],
            'wingae_size' => [
                'class' => OptionsBehavior::className(),
                'attribute' => 'wingae_size',
                'options' => [
                    static::SIZE_SMALL => Yii::t('app', 'Small'),
                    static::SIZE_MEDIUM => Yii::t('app', 'Medium'),
                    static::SIZE_LARGE => Yii::t('app', 'Large'),
                    static::SIZE_EXTRA_LARGE => Yii::t('app', 'Extra Large'),
                ]
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
        return [
            'id' => Yii::t('app', 'ID'),
            'parent_id' => Yii::t('app', 'Parent Category'),
            'name' => Yii::t('app', 'Name'),
            'ui_layout' => Yii::t('app', 'Sub Layout'),
            'image' => Yii::t('app', 'Image'),
            'icon' => Yii::t('app', 'Icon'),
            'enable' => Yii::t('app', 'Status'),
            'enable_label' => Yii::t('app', 'Status'),
            'shortname' => Yii::t('app', 'Short Name'),
            'question' => Yii::t('app', 'Question'),
            'sla_collect' => Yii::t('app/services', 'SLA Collection'),
            'sla_complete' => Yii::t('app/services', 'SLA Completion'),
            'sla_return' => Yii::t('app/services', 'SLA Return'),
            'mobile_description' => Yii::t('app/services', 'Short description'),
            'category_order' => Yii::t('app', 'Category Order'),
            'is_visible' => Yii::t('app', 'Visibility'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'note_on_request_title' => Yii::t('app', 'Note Title'),
            'note_on_request' => Yii::t('app', 'New Request Title'),
            'wingae_size' => Yii::t('app', 'Size for wingae'),
            'is_deal' => 'Include in Deals Section',
            'deal_image' => 'Upload Deals Image'

        ];
    }

    public function attributeHints()
    {
        return [
            'icon' => Yii::t('app', '512x512px .SVG file'),
            'ui_layout' => Yii::t('app', 'If this category contains subcategories how should they be listed in the catalog'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getServices()
    {
        return $this->hasMany(Service::className(), ['parent_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(ServiceCategory::className(), ['id' => 'parent_id']);
    }

    public function getLastupdatedby()
    {
        return $this->hasOne(Admin::className(), ['id' => 'last_updated_by']);
    }

    /**
     * @return ActiveQuery
     */
    public function getServiceCategories()
    {
        return $this->hasMany(ServiceCategory::className(), ['parent_id' => 'id']);
    }

    /**
     *
     * @param string $slug
     * @return ServiceCategory
     */
    public static function findBySlug($slug)
    {
        return static::findOne(["slug" => $slug, 'enable' => self::STATUS_ENABLED]);
    }

    /**
     * get all top level service categories (that does not have parent_id)
     * @return ServiceCategory
     */
    public static function getTopLevelCategories()
    {
        return static::findAll(["enable" => self::STATUS_ENABLED, 'parent_id' => null]);
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
        } else if ($this->parent) {
            return $this->parent->noteOnRequest;
        }
        return array();
    }

    public function foreachParentDo($callback)
    {
        $parent = $this->parent;
        if (isset($parent)) {
            $parent->foreachParentDo($callback);
            $callback($parent);
        }
    }

    public function __get($name)
    {
        if ($name == "icon_path") {
            if (isset($this->icon) && file_exists($this->getUploadedFilePath('icon'))) {
                return file_get_contents($this->getUploadedFilePath('icon'));
            }
        }
        return parent::__get($name);
    }

    public function getCommissionRate()
    {
        if (isset($this->commission_rate)) {
            return $this->commission_rate;
        }
        if ($this->parent_id != null) {
            $parent = $this->parent;
            return $parent->getCommissionRate();
        }
        return SystemSetting::getValue("commision_rate");
    }

    public function getCommissionRateText($parent = false)
    {
        if (isset($this->commission_rate)) {
            if ($parent) {
                return "$this->commission_rate% ($this->name)";
            } else {
                return "$this->commission_rate%";
            }
        }
        if ($this->parent_id != null) {
            $parent = $this->parent;
            return $parent->getCommissionRateText(true);
        }
        return "System Default (" . SystemSetting::getValue("commision_rate") . "%)";
    }

    public function getSLACollect()
    {
        if (isset($this->sla_collect)) {
            return $this->sla_collect;
        }
        if ($this->parent_id != null) {
            $parent = $this->parent;
            return $parent->getSLACollect();
        }
        return SystemSetting::getValue("sla_collect");
    }

    public function getSLAComplete()
    {
        if (isset($this->sla_complete)) {
            return $this->sla_complete;
        }
        if ($this->parent_id != null) {
            $parent = $this->parent;
            return $parent->getSLAComplete();
        }
        return SystemSetting::getValue("sla_complete");
    }

    public function getSLAReturn()
    {
        if (isset($this->sla_return)) {
            return $this->sla_return;
        }
        if ($this->parent_id != null) {
            $parent = $this->parent;
            return $parent->getSLAReturn();
        }
        return SystemSetting::getValue("sla_return");
    }

    /**
     * return the path to the icon representing this category or its parents
     * @return string
     */
    public function getIconPath($forChild = false)
    {
        if ((isset($this->icon) && file_exists($this->getUploadedFilePath('icon'))) &&
            (!$forChild || ($forChild && $this->use_icon_for_childs))
        ) {
            return $this->getUploadedFilePath('icon');
        }
        if ($this->parent_id != null) {
            return $this->parent->getIconPath($forChild);
        }
        return Yii::getAlias("@staticRoot") . DIRECTORY_SEPARATOR . "default" . DIRECTORY_SEPARATOR . 'icon.svg';
    }

    public static function getSubServicesIds($category_id)
    {
        $servicesIds = [];
        /* @var $category ServiceCategory[] */
        $category = ServiceCategory::find()
            ->multilingual()
            ->where(['id' => $category_id])
            ->one();
        if (count($category->services) > 0) {
            $servicesIds = array_merge($servicesIds, ArrayHelper::getColumn($category->services, "id", false));
        }
        if (count($category->serviceCategories) > 0) {
            foreach ($category->serviceCategories as $key => $cat) {
                $servicesIds = array_merge($servicesIds, static::getSubServicesIds($cat->id));
            }
        }
        return $servicesIds;
    }

    public function updateCatalog()
    {
        if ($this->is_visible) {
            SystemSetting::increment("catalog_version");
        }
    }

    public static function toList()
    {
        return Collection::make(static::find()
            ->where(['enable' => true])
            ->andWhere(['IS', 'parent_id', NULL])
            ->asArray()->all())
            ->pluck('name', 'name')
            ->toArray();
    }

    public static function toDropDownList()
    {
        return Collection::make(static::find()
            ->where(['enable' => true])
            ->asArray()->all())
            ->pluck('name', 'id')
            ->toArray();
    }

    public function getSliders()
    {
        return $this->hasMany(ServiceCategorySlider::className(), ['service_category_id' => 'id']);
    }
    public function getType () {
        return 'category';
    }
}
