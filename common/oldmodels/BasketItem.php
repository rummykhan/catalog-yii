<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace common\oldmodels;

use omgdef\multilingual\MultilingualBehavior;
use omgdef\multilingual\MultilingualQuery;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yiidreamteam\upload\ImageUploadBehavior;

/**
 * Description of BasketItem
 *
 * @author Tarek K. Ajaj
 * Jul 20, 2016 11:35:45 AM
 * 
 * BasketItem.php
 * UTF-8
 * 
 */

/**
 * This is the model class for table "basket_item".
 *
 * @property integer $id
 * @property integer $basket_grid_id
 * @property string $value
 * @property string $image
 * @property integer $option_order
 * @property string $mobile_icon
 * @property string $mobile_description
 *
 * @property BasketCellMap[] $basketCellMaps
 * @property BasketGrid $basketGrid
 * @property BasketItemLang[] $basketItemLangs
 */
class BasketItem extends ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'basket_item';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['basket_grid_id', 'option_order'], 'integer'],
            [['value'], 'string', 'max' => 255],
            [['image'], 'file', 'extensions' => 'jpg,png,jpeg', 'maxSize' => (1024 * 1024) * 4],
            [['basket_grid_id'], 'exist', 'skipOnError' => true, 'targetClass' => BasketGrid::className(), 'targetAttribute' => ['basket_grid_id' => 'id']],
            ['mobile_icon', 'file', 'extensions' => 'jpg,png', 'maxSize' => 512 * 1024],
            [['mobile_description'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'basket_grid_id' => 'Basket Grid ID',
            'value' => 'Value',
        ];
    }

    public static function getDB()
    {
        return Yii::$app->get("old");
    }

    public function behaviors() {
        return [
            [
                'class' => ImageUploadBehavior::className(),
                'attribute' => 'image',
                'thumbs' => [
                    'thumb' => ['width' => 200, 'height' => 200],
                ],
                'filePath' => '@uploadRoot/images/basketitem/item_[[pk]].[[extension]]',
                'fileUrl' => '@uploadWeb/images/basketitem/item_[[pk]].[[extension]]',
                'thumbPath' => '@uploadRoot/images/basketitem/[[profile]]_item_[[pk]].[[extension]]',
                'thumbUrl' => '@uploadWeb/images/basketitem/[[profile]]_item_[[pk]].[[extension]]',
            ],
            'mobile_icon' => [
                'class' => ImageUploadBehavior::className(),
                'attribute' => 'mobile_icon',
                'thumbs' => [
                    'thumb' => ['width' => 128, 'height' => 128],
                ],
                'filePath' => '@uploadRoot/images/services_attr_bi/service_attr_bi_icon_[[pk]].[[extension]]',
                'fileUrl' => '@uploadWeb/images/services_attr_bi/service_attr_icon_bi_[[pk]].[[extension]]',
                'thumbPath' => '@uploadRoot/images/services_attr_bi/[[profile]]_service_attr_bi_icon_[[pk]].[[extension]]',
                'thumbUrl' => '@uploadWeb/images/services_attr_bi/[[profile]]_service_attr_bi_icon_[[pk]].[[extension]]',
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
     * @return ActiveQuery
     */
    public function getBasketCellMaps() {
        return $this->hasMany(BasketCellMap::className(), ['basket_item_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getBasketGrid() {
        return $this->hasOne(BasketGrid::className(), ['id' => 'basket_grid_id']);
    }

}
