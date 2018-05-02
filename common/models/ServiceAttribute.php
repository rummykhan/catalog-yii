<?php

namespace common\models;

use common\queries\NotDeletedQuery;
use frontend\helpers\FieldsConfigurationHelper;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\db\Query;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "service_attribute".
 *
 * @property int $id
 * @property int $service_id
 * @property string $name
 * @property int $input_type_id
 * @property int $user_input_type_id
 * @property string $validationsString
 * @property boolean $deleted
 * @property int $field_type_id
 *
 * @property ServiceAttributeDepends[] $parental
 * @property ServiceAttributeDepends[] $dependants
 * @property Service $service
 * @property ServiceAttributeOption[] $serviceAttributeOptions
 * @property Validation[] $validations
 * @property PricingAttribute[] $pricingAttributes
 */
class ServiceAttribute extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'service_attribute';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['service_id'], 'integer'],
            [['name'], 'required'],
            [['name'], 'safe'],
            [['deleted'], 'boolean'],
            [['service_id'], 'exist', 'skipOnError' => true, 'targetClass' => Service::className(), 'targetAttribute' => ['service_id' => 'id']],
            [['input_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => InputType::className(), 'targetAttribute' => ['input_type_id' => 'id']],
            [['user_input_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserInputType::className(), 'targetAttribute' => ['user_input_type_id' => 'id']],
            [['field_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => FieldType::className(), 'targetAttribute' => ['field_type_id' => 'id']],
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
            'name' => 'Field Name'
        ];
    }

    /**
     * @return ActiveQuery
     */
    public static function find()
    {
        return new NotDeletedQuery(get_called_class());
    }

    public function getInputType()
    {
        return $this->hasOne(InputType::className(), ['id' => 'input_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getService()
    {
        return $this->hasOne(Service::className(), ['id' => 'service_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceAttributeOptions()
    {
        return $this->hasMany(ServiceAttributeOption::className(), ['service_attribute_id' => 'id']);
    }

    public function getPricingAttributes()
    {
        return $this->hasMany(PricingAttribute::className(), ['service_attribute_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserInputType()
    {
        return $this->hasOne(UserInputType::className(), ['id' => 'user_input_type_id']);
    }

    public function getFieldType()
    {
        return $this->hasOne(FieldType::className(), ['id' => 'field_type_id']);
    }

    /**
     * @return mixed
     */
    public function getOptionsList()
    {
        $query = (new Query())
            ->select(['attribute_option.name', 'service_attribute_option.id'])
            ->from('service_attribute_option')
            ->join('inner join', 'attribute_option', 'service_attribute_option.attribute_option_id=attribute_option.id')
            ->where(['service_attribute_option.service_attribute_id' => $this->id]);

        return collect($query->all())->pluck('name', 'id');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParental()
    {
        return $this->hasMany(ServiceAttributeDepends::className(), ['service_attribute_id' => 'id']);
    }

    public function getDependants()
    {
        return $this->hasMany(ServiceAttributeDepends::className(), ['depends_on_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getValidations()
    {
        return $this->hasMany(Validation::className(), ['id' => 'validation_id'])
            ->viaTable('service_attribute_validation', ['service_attribute_id' => 'id']);
    }

    public function getValidationsString()
    {
        $attributeValidation = $this->getValidations()->asArray()->all();

        return implode(',', collect($attributeValidation)->pluck('type')->toArray());
    }

    public function getMinimum()
    {
        return $this->getServiceAttributeOptions()->where(['deleted' => false])->one();
    }

    public function getMaximum()
    {
        return $this->getServiceAttributeOptions()->where(['deleted' => false])->orderBy(['id' => SORT_DESC])->one();
    }
}
