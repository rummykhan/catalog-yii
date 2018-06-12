<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace common\oldmodels;

use common\models\services\ServiceAttribute;
use omgdef\multilingual\MultilingualBehavior;
use omgdef\multilingual\MultilingualQuery;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yiidreamteam\upload\ImageUploadBehavior;

/**
 * Description of BasketGrid
 *
 * @author Tarek K. Ajaj
 * Jul 20, 2016 11:35:08 AM
 * 
 * BasketGrid.php
 * UTF-8
 * 
 */

/**
 * This is the model class for table "basket_grid".
 *
 * @property integer $id
 * @property integer $service_attribute_id
 * @property string $basket_items_label
 * @property string $basket_services_label
 * @property integer $basket_limit
 * @property string $quantity_unit
 * @property string $icon
 * @property string $multi_selection
 * @property string $icon_path
 * @property integer $min_qty
 * @property integer $max_qty
 *
 * @property ServiceAttribute $serviceAttribute
 * @property BasketGridLang[] $basketGridLangs
 * @property BasketItem[] $basketItems
 * @property BasketItem[] $basketItemsMultilingual
 * @property BasketService[] $basketServices
 * @property BasketService[] $basketServicesMultilingual
 */
class BasketGrid extends ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'basket_grid';
    }

    public static function getDB()
    {
        return Yii::$app->get("old");
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['service_attribute_id', 'basket_limit'], 'integer'],
            [['basket_items_label', 'basket_services_label', 'quantity_unit'], 'string', 'max' => 255],
            [['multi_selection'], 'boolean'],
            [['min_qty', 'max_qty'], 'integer'],
            [['icon', 'icon_path'], 'safe'],
            ['icon', 'file', 'extensions' => 'jpg,png,svg', 'maxSize' => (1024 * 1024) * 4],
                //[['service_attribute_id'], 'exist', 'skipOnError' => true, 'targetClass' => ServiceAttribute::className(), 'targetAttribute' => ['service_attribute_id' => 'id']],
        ];
    }

    public function behaviors() {
        return [
            [
                'class' => ImageUploadBehavior::className(),
                'attribute' => 'icon',
                'filePath' => '@uploadRoot/icons/icon_basket_[[pk]].[[extension]]',
                'fileUrl' => '@uploadWeb/icons/icon_basket_[[pk]].[[extension]]',
            ],
        ];
    }

    /**
     * @inheritdoc
     * 
     * @return MultilingualQuery
     */
    public static function find() {
        return new MultilingualQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'service_attribute_id' => 'Service Attribute ID',
            'basket_items_label' => 'Basket Items Label',
            'basket_services_label' => 'Basket Services Label',
        ];
    }


    /**
     * @return ActiveQuery
     */
    public function getBasketItems() {
        return $this->hasMany(BasketItem::className(), ['basket_grid_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getBasketServices() {
        return $this->hasMany(BasketService::className(), ['basket_grid_id' => 'id']);
    }

    public function getBasketServicesMultilingual() {
        return $this->getBasketServices()->multilingual();
    }

}
