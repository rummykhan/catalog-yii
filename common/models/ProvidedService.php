<?php

namespace common\models;

use RummyKhan\Collection\Arr;
use RummyKhan\Collection\Collection;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
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
 * @property ProvidedServiceType[] $providedServiceTypes
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

    /**
     * @return ActiveQuery
     */
    public function getProvidedServiceTypes()
    {
        return $this->hasMany(ServiceType::className(), ['id' => 'service_type_id'])
            ->viaTable('provided_service_type', ['provided_service_id' => 'id']);
    }

    public function getProvidedServiceTypesList()
    {
        return collect(
            $this->getProvidedServiceTypes()->asArray()->all()
        )->pluck('type', 'id');
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

    /**
     * @param $services array
     * @return mixed
     */
    public function provideServices($services)
    {
        if (empty($services)) {
            return false;
        }

        foreach ($services as $service) {
            $this->provideService($service);
        }

        return true;
    }

    public function provideService($service_id)
    {
        $providedService = ProvidedService::find()
            ->where(['provider_id' => $this->provider_id])
            ->andWhere(['service_id' => $service_id])
            ->one();

        if ($providedService) {
            return false;
        }

        $providedService = new ProvidedService();
        $providedService->service_id = $service_id;
        $providedService->provider_id = $this->provider_id;
        $providedService->save();

        return true;
    }

    public function saveNoImpactPrices($prices, $area_id)
    {

        //dd($prices, $area_id);
    }

    public function saveMatrixPrices($prices, $area_id)
    {
        if (empty($prices)) {
            return false;
        }

        foreach ($prices as $matrix => $price) {
            $this->saveMatrixPrice(explode('_', $matrix), $price, $area_id);
        }

        return true;
    }

    public function saveMatrixPrice($matrix, $price, $area_id)
    {
        $parent_id = static::getParent($matrix, $this->service_id);

        if (empty($parent_id)) {
            return false;
        }

        /** @var PricingAttributeParent $parent */
        $parent = PricingAttributeParent::findOne($parent_id);
        if (!$parent) {
            return false;
        }

        if (!$parent->providedServiceMatrixPricing) {
            $matrixPricing = new ProvidedServiceMatrixPricing();
        } else {
            $matrixPricing = $parent->providedServiceMatrixPricing;
        }

        $matrixPricing->provided_service_id = $this->id;
        $matrixPricing->pricing_attribute_parent_id = $parent_id;
        $matrixPricing->price = $price;
        $matrixPricing->provided_service_area_id = $area_id;

        if (empty($price)) {
            $matrixPricing->delete();
        } else {
            $matrixPricing->save();
        }
    }

    public static function getParent($matrix, $service_id)
    {
        $parents = static::getServiceParentAttributePricingGroup($service_id);

        if (empty($parents)) {
            return null;
        }

        /** @var Collection $parent */
        foreach ($parents as $id => $parent) {

            $found = [];
            foreach ($matrix as $item) {
                $found[] = null !== $parent->where('service_attribute_option_id', $item)->first();
            }

            $found = array_unique($found);

            if (count($found) > 1) {
                continue;
            }

            $found = Arr::first($found);

            if ($found) {
                return $id;
            }
        }

        return null;
    }

    public static function getServiceParentAttributePricingGroup($service_id)
    {
        $query = (new Query())
            ->select([
                new Expression('pricing_attribute_matrix.pricing_attribute_parent_id'),
                new Expression('pricing_attribute_matrix.service_attribute_option_id')
            ])->from('pricing_attribute_matrix')
            ->join('inner join', 'pricing_attribute_parent', 'pricing_attribute_matrix.pricing_attribute_parent_id=pricing_attribute_parent.id')
            ->where(['pricing_attribute_parent.service_id' => $service_id]);

        return collect($query->all())->groupBy('pricing_attribute_parent_id');
    }

    public function getPriceOfMatrixRow($row)
    {
        $parent_id = static::getParent($row, $this->service_id);

        if (empty($parent_id)) {
            return null;
        }

        /** @var PricingAttributeParent $parent */
        $parent = PricingAttributeParent::findOne($parent_id);
        if (!$parent) {
            return null;
        }

        if (!$parent->providedServiceMatrixPricing) {
            return null;
        }

        return $parent->providedServiceMatrixPricing->price;

    }
}
