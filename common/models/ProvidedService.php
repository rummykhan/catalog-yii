<?php

namespace common\models;

use common\helpers\Matrix;
use common\helpers\ServiceAttributeMatrix;
use RummyKhan\Collection\Arr;
use RummyKhan\Collection\Collection;
use Yii;
use yii\base\Exception;
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

    public function saveIndependentPrices($prices, $area_id)
    {
        if (empty($prices)) {
            return true;
        }

        foreach ($prices as $service_attribute_option_id => $price) {

            // check if this option is not deleted and price can be set.
            $query = (new Query())
                ->select(['service_attribute_option.id'])
                ->from('service_attribute_option')
                ->join('inner join', 'service_attribute', 'service_attribute_option.service_attribute_id=service_attribute.id')
                ->join('inner join', 'pricing_attribute', 'service_attribute.id=pricing_attribute.service_attribute_id')
                ->join('inner join', 'service', 'service_attribute.service_id=service.id')
                ->join('inner join', 'provided_service', 'service.id=provided_service.service_id')
                ->where(['service.id' => $this->service_id])
                ->andWhere(['provided_service.provider_id' => $this->provider_id])
                ->andWhere(['service_attribute_option.id' => $service_attribute_option_id])
                ->andWhere(['service_attribute_option.deleted' => false])
                ->andWhere(['service_attribute.deleted' => false]);

            if (!$query->one()) {

                // this option doesn't belong to this service
                // maybe we can throw exceptions

                continue;
            }


            // check if already inserted ?
            $query = (new Query())
                ->select(['provided_service_independent_pricing.id'])
                ->from('provided_service_independent_pricing')
                ->join('inner join', 'pricing_attribute', 'provided_service_independent_pricing.pricing_attribute_id=pricing_attribute.id')
                ->join('inner join', 'service_attribute', 'pricing_attribute.service_attribute_id=service_attribute.id')
                ->join('inner join', 'service_attribute_option', 'service_attribute.id=service_attribute_option.service_attribute_id')
                ->join('inner join', 'service', 'service_attribute.service_id=service.id')
                ->join('inner join', 'provided_service', 'service.id=provided_service.service_id')
                ->where(['service.id' => $this->service_id])
                ->andWhere(['provided_service.provider_id' => $this->provider_id])
                ->andWhere(['service_attribute_option.id' => $service_attribute_option_id])
                ->andWhere(['provided_service_independent_pricing.service_attribute_option_id' => $service_attribute_option_id])
                ->andWhere(['service_attribute_option.deleted' => false])
                ->andWhere(['service_attribute.deleted' => false])
                ->andWhere(['provided_service_independent_pricing.provided_service_area_id' => $area_id]);

            $result = $query->one();

            $basePricing = null;
            if ($result) {
                $basePricing = ProvidedServiceIndependentPricing::findOne($result['id']);
            } else {
                $basePricing = new ProvidedServiceIndependentPricing();
            }

            $query = (new Query())
                ->select(['pricing_attribute.id'])
                ->from('pricing_attribute')
                ->join('inner join', 'service_attribute', 'pricing_attribute.service_attribute_id=service_attribute.id')
                ->join('inner join', 'service_attribute_option', 'service_attribute.id=service_attribute_option.service_attribute_id')
                ->andWhere(['service_attribute.deleted' => false])
                ->andWhere(['service_attribute_option.deleted' => false])
                ->andWhere(['service_attribute_option.id' => $service_attribute_option_id]);

            $pricingAttribute = $query->one();
            if (!$pricingAttribute) {
                throw new Exception('Pricing matrix not confirmed.');
            }

            $basePricing->service_attribute_option_id = $service_attribute_option_id;
            $basePricing->provided_service_area_id = $area_id;
            $basePricing->pricing_attribute_id = $pricingAttribute['id'];
            $basePricing->base_price = $price;
            $basePricing->save();
        }
    }

    public function saveNoImpactPrices($prices, $area_id)
    {
        if (empty($prices)) {
            return true;
        }

        foreach ($prices as $service_attribute_option_id => $price) {

            // check if this option is not deleted and price can be set.
            $query = (new Query())
                ->select(['service_attribute_option.id'])
                ->from('service_attribute_option')
                ->join('inner join', 'service_attribute', 'service_attribute_option.service_attribute_id=service_attribute.id')
                ->join('inner join', 'pricing_attribute', 'service_attribute.id=pricing_attribute.service_attribute_id')
                ->join('inner join', 'service', 'service_attribute.service_id=service.id')
                ->join('inner join', 'provided_service', 'service.id=provided_service.service_id')
                ->where(['service.id' => $this->service_id])
                ->andWhere(['provided_service.provider_id' => $this->provider_id])
                ->andWhere(['service_attribute_option.id' => $service_attribute_option_id])
                ->andWhere(['service_attribute_option.deleted' => false])
                ->andWhere(['service_attribute.deleted' => false]);

            if (!$query->one()) {

                // this option doesn't belong to this service
                // maybe we can throw exceptions

                continue;
            }


            // check if already inserted ?
            $query = (new Query())
                ->select(['provided_service_no_impact_pricing.id'])
                ->from('provided_service_no_impact_pricing')
                ->join('inner join', 'pricing_attribute', 'provided_service_no_impact_pricing.pricing_attribute_id=pricing_attribute.id')
                ->join('inner join', 'service_attribute', 'pricing_attribute.service_attribute_id=service_attribute.id')
                ->join('inner join', 'service_attribute_option', 'service_attribute.id=service_attribute_option.service_attribute_id')
                ->join('inner join', 'service', 'service_attribute.service_id=service.id')
                ->join('inner join', 'provided_service', 'service.id=provided_service.service_id')
                ->where(['service.id' => $this->service_id])
                ->andWhere(['provided_service.provider_id' => $this->provider_id])
                ->andWhere(['service_attribute_option.id' => $service_attribute_option_id])
                ->andWhere(['provided_service_no_impact_pricing.service_attribute_option_id' => $service_attribute_option_id])
                ->andWhere(['service_attribute_option.deleted' => false])
                ->andWhere(['service_attribute.deleted' => false])
                ->andWhere(['provided_service_no_impact_pricing.provided_service_area_id' => $area_id]);

            $result = $query->one();

            $basePricing = null;
            if ($result) {
                $basePricing = ProvidedServiceNoImpactPricing::findOne($result['id']);
            } else {
                $basePricing = new ProvidedServiceNoImpactPricing();
            }

            $query = (new Query())
                ->select(['pricing_attribute.id'])
                ->from('pricing_attribute')
                ->join('inner join', 'service_attribute', 'pricing_attribute.service_attribute_id=service_attribute.id')
                ->join('inner join', 'service_attribute_option', 'service_attribute.id=service_attribute_option.service_attribute_id')
                ->andWhere(['service_attribute.deleted' => false])
                ->andWhere(['service_attribute_option.deleted' => false])
                ->andWhere(['service_attribute_option.id' => $service_attribute_option_id]);

            $pricingAttribute = $query->one();
            if (!$pricingAttribute) {
                throw new Exception('Pricing matrix not confirmed.');
            }

            $basePricing->service_attribute_option_id = $service_attribute_option_id;
            $basePricing->provided_service_area_id = $area_id;
            $basePricing->pricing_attribute_id = $pricingAttribute['id'];
            $basePricing->save();
        }
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

        $matrixPricing = $parent->getProvidedServiceCompositePricing()->where(['provided_service_area_id' => $area_id])->one();

        if (!$matrixPricing) {
            $matrixPricing = new ProvidedServiceCompositePricing();
        }

        $matrixPricing->pricing_attribute_parent_id = $parent->id;
        $matrixPricing->price = $price;
        $matrixPricing->provided_service_area_id = $area_id;

        if (empty($price)) {
            $matrixPricing->delete();
        } else {
            $matrixPricing->save();
        }
    }

    public static function createPricingAttributeParent($matrix, $service_id)
    {
        if (empty($matrix)) {
            return null;
        }

        $parent = new PricingAttributeParent();
        $parent->service_id = $service_id;
        $parent->save();

        foreach ($matrix as $item) {
            $pricingAttributeMatrix = new PricingAttributeMatrix();
            $pricingAttributeMatrix->pricing_attribute_parent_id = $parent->id;
            $pricingAttributeMatrix->service_attribute_option_id = $item;
            $pricingAttributeMatrix->save();
        }

        return $parent;
    }

    public static function getParent($matrix, $service_id)
    {
        $parent_id = static::getServiceParentAttributePricingGroup($matrix, $service_id);

        if (!empty($parent_id)) {
            return $parent_id;
        }

        $parent = static::createPricingAttributeParent($matrix, $service_id);

        return $parent ? $parent->id : null;
    }

    public static function getServiceParentAttributePricingGroup($matrix, $service_id)
    {
        $query = (new Query())
            ->select([
                new Expression('pricing_attribute_matrix.pricing_attribute_parent_id'),
                new Expression('count(pricing_attribute_matrix.service_attribute_option_id) as count')
            ])->from('pricing_attribute_matrix')
            ->join('inner join', 'pricing_attribute_parent', 'pricing_attribute_matrix.pricing_attribute_parent_id=pricing_attribute_parent.id')
            ->where(['pricing_attribute_parent.service_id' => $service_id])
            ->andWhere(['IN', 'pricing_attribute_matrix.service_attribute_option_id', $matrix])
            ->groupBy(['pricing_attribute_matrix.pricing_attribute_parent_id'])
            ->having(['=', 'count', count($matrix)]);

        $results = $query->all();

        if (count($results) === 0) {
            return null;
        }


        foreach ($results as $index => $result) {
            if ($index === 0) {
                continue;
            }

            PricingAttributeMatrix::deleteAll([
                'pricing_attribute_parent_id' => $result['pricing_attribute_parent_id']
            ]);
        }

        return Arr::first($results)['pricing_attribute_parent_id'];
    }

    public function getPriceOfMatrixRow($row, $area_id)
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

        /** @var ProvidedServiceCompositePricing $matrixPricing */
        $matrixPricing = $parent->getProvidedServiceCompositePricing()->where(['provided_service_area_id' => $area_id])->one();

        if (!$matrixPricing) {
            return null;
        }

        return $matrixPricing->price;
    }

    /**
     * @return ProvidedServiceType[]
     */
    public function getUnDeletedServiceTypes()
    {
        return ProvidedServiceType::find()
            ->where(['provided_service_id' => $this->id])
            ->andWhere(['deleted' => false])
            ->all();
    }

    public function getPriceOfNoImpactRow($service_attribute_option_id, $area_id)
    {
        $query = (new Query())
            ->select(['provided_service_independent_pricing.base_price'])
            ->from('provided_service_independent_pricing')
            ->join('inner join', 'pricing_attribute', 'provided_service_independent_pricing.pricing_attribute_id=pricing_attribute.id')
            ->join('inner join', 'service_attribute', 'pricing_attribute.service_attribute_id=service_attribute.id')
            ->join('inner join', 'service_attribute_option', 'service_attribute.id=service_attribute_option.service_attribute_id')
            ->join('inner join', 'service', 'service_attribute.service_id=service.id')
            ->join('inner join', 'provided_service', 'service.id=provided_service.service_id')
            ->where(['service.id' => $this->service_id])
            ->andWhere(['provided_service.provider_id' => $this->provider_id])
            ->andWhere(['service_attribute_option.id' => $service_attribute_option_id])
            ->andWhere(['provided_service_independent_pricing.service_attribute_option_id' => $service_attribute_option_id])
            ->andWhere(['service_attribute_option.deleted' => false])
            ->andWhere(['service_attribute.deleted' => false])
            ->andWhere(['provided_service_independent_pricing.provided_service_area_id' => $area_id]);

        $results = $query->one();

        if (!$results || !isset($results['base_price'])) {
            return null;
        }

        return $results['base_price'];
    }

    public function getPriceOfIndependentRow($service_attribute_option_id, $area_id)
    {
        $query = (new Query())
            ->select(['provided_service_independent_pricing.base_price'])
            ->from('provided_service_independent_pricing')
            ->join('inner join', 'pricing_attribute', 'provided_service_independent_pricing.pricing_attribute_id=pricing_attribute.id')
            ->join('inner join', 'service_attribute', 'pricing_attribute.service_attribute_id=service_attribute.id')
            ->join('inner join', 'service_attribute_option', 'service_attribute.id=service_attribute_option.service_attribute_id')
            ->join('inner join', 'service', 'service_attribute.service_id=service.id')
            ->join('inner join', 'provided_service', 'service.id=provided_service.service_id')
            ->where(['service.id' => $this->service_id])
            ->andWhere(['provided_service.provider_id' => $this->provider_id])
            ->andWhere(['service_attribute_option.id' => $service_attribute_option_id])
            ->andWhere(['provided_service_independent_pricing.service_attribute_option_id' => $service_attribute_option_id])
            ->andWhere(['service_attribute_option.deleted' => false])
            ->andWhere(['service_attribute.deleted' => false])
            ->andWhere(['provided_service_independent_pricing.provided_service_area_id' => $area_id]);

        $results = $query->one();

        if (!$results || !isset($results['base_price'])) {
            return null;
        }

        return $results['base_price'];
    }

    /**
     * @param $prices array
     * @param $area ProvidedServiceArea
     */
    public function savePrices($prices, $area, $hash)
    {
        $motherMatrix = new ServiceAttributeMatrix($this->service);

        /** @var Matrix $matrix */
        foreach ($motherMatrix->getMatrices() as $matrix) {

            if ($matrix->isEqual($hash)) {
                $matrix->deleteAreaPrices($area->id);
            }
        }

        $matrixPrices = Arr::get($prices, 'matrix_price');
        $independentPrices = Arr::get($prices, 'independent_price');
        $noImpactPrices = Arr::get($prices, 'no_impact_price');

        $this->saveMatrixPrices($matrixPrices, $area->id);
        $this->saveIndependentPrices($independentPrices, $area->id);
        $this->saveNoImpactPrices($noImpactPrices, $area->id);
    }

    public function isNoImpactOptionEnabled($area_id, $service_attribute_option_id)
    {
        $query = (new Query())
            ->select(['provided_service_no_impact_pricing.*'])
            ->from('provided_service_no_impact_pricing')
            ->join('inner join', 'pricing_attribute', 'provided_service_no_impact_pricing.pricing_attribute_id=pricing_attribute.id')
            ->join('inner join', 'service_attribute', 'pricing_attribute.service_attribute_id=service_attribute.id')
            ->join('inner join', 'service_attribute_option', 'service_attribute.id=service_attribute_option.service_attribute_id')
            ->join('inner join', 'service', 'service_attribute.service_id=service.id')
            ->join('inner join', 'provided_service', 'service.id=provided_service.service_id')
            ->where(['service.id' => $this->service_id])
            ->andWhere(['provided_service.provider_id' => $this->provider_id])
            ->andWhere(['service_attribute_option.id' => $service_attribute_option_id])
            ->andWhere(['provided_service_no_impact_pricing.service_attribute_option_id' => $service_attribute_option_id])
            ->andWhere(['service_attribute_option.deleted' => false])
            ->andWhere(['service_attribute.deleted' => false])
            ->andWhere(['provided_service_no_impact_pricing.provided_service_area_id' => $area_id]);

        return !!$query->one();
    }
}
