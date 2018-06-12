<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace common\oldmodels;

use common\models\services\ServiceAttributeOption;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Description of BasketCellMap
 *
 * @author Tarek K. Ajaj
 * Jul 20, 2016 11:37:06 AM
 * 
 * BasketCellMap.php
 * UTF-8
 * 
 */

/**
 * This is the model class for table "basket_cell_map".
 *
 * @property integer $id
 * @property integer $service_attribute_option_id
 * @property integer $basket_item_id
 * @property integer $basket_service_id
 *
 * @property BasketItem $basketItem
 * @property ServiceAttributeOption $serviceAttributeOption
 * @property ServiceAttributeOption $serviceAttributeOptionMultilingual
 * @property BasketService $basketService
 */
class BasketCellMap extends ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'basket_cell_map';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['service_attribute_option_id', 'basket_item_id', 'basket_service_id'], 'integer'],
            [['basket_item_id'], 'exist', 'skipOnError' => true, 'targetClass' => BasketItem::className(), 'targetAttribute' => ['basket_item_id' => 'id']],
            [['service_attribute_option_id'], 'exist', 'skipOnError' => true, 'targetClass' => ServiceAttributeOption::className(), 'targetAttribute' => ['service_attribute_option_id' => 'id']],
            [['basket_service_id'], 'exist', 'skipOnError' => true, 'targetClass' => BasketService::className(), 'targetAttribute' => ['basket_service_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'service_attribute_option_id' => 'Service Attribute Option ID',
            'basket_item_id' => 'Basket Item ID',
            'basket_service_id' => 'Basket Service ID',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getBasketItem() {
        return $this->hasOne(BasketItem::className(), ['id' => 'basket_item_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getServiceAttributeOption() {
        return $this->hasOne(ServiceAttributeOption::className(), ['id' => 'service_attribute_option_id']);
    }
    public function getServiceAttributeOptionMultilingual() {
        return $this->getServiceAttributeOption()->multilingual();
    }

    /**
     * @return ActiveQuery
     */
    public function getBasketService() {
        return $this->hasOne(BasketService::className(), ['id' => 'basket_service_id']);
    }

}
