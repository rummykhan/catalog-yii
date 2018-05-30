<?php

namespace common\helpers;


use common\models\PriceType;
use common\models\PricingAttribute;
use common\models\PricingAttributeGroup;
use common\models\PricingAttributeMatrix;
use common\models\PricingAttributeParent;
use common\models\ProvidedServiceIndependentPricing;
use common\models\ProvidedServiceCompositePricing;
use common\models\ProvidedServiceNoImpactPricing;
use common\models\Service;
use RummyKhan\Collection\Collection;

class Matrix
{
    /**
     * @var Service $service
     */
    protected $service;

    /**
     * @var PricingAttributeGroup
     */
    protected $priceGroupID;

    /**
     * @var array $headers
     */
    private $matrixHeaders;

    /**
     * @var array $results
     */
    private $matrixRows;

    /**
     * @var array $results
     */
    private $noImpactRows;

    /**
     * @var array $results
     */
    private $independentRows;

    /**
     * @var array $results
     */
    private $compositeAttributes;

    /**
     * @var array $incrementalAttributes
     */
    private $incrementalAttributes;

    /**
     * @var array $groups
     */
    private $groups;

    /**
     * @var array $rounds
     */
    private $rounds = [];

    /**
     * @var array
     */
    private $roundsLimits = [];

    /**
     * @var Collection
     */
    private $attributeGroups;

    /**
     * @var
     */
    private $rowIdentifiers;

    /**
     * @var
     */
    private $serviceAttributes;

    /**
     * @var
     */
    private $hash;

    /**
     * MatrixHelper constructor.
     * @param $service Service
     * @param $priceGroupID int
     */
    public function __construct($service, $priceGroupID = null)
    {
        $this->service = $service;
        $this->priceGroupID = $priceGroupID;

        $this->configureCompositeAttributes();
        $this->configureNoImpactAttributes();
        $this->configureIndependentAttributes();
        $this->configureIncrementalAttributes();

        $this->createMatrix();

        $this->createHash();
    }

    /**
     * Create Matrix Clock using composite attributes
     */
    private function configureCompositeAttributes()
    {
        $priceType = PriceType::find()->where(['type' => PriceType::TYPE_COMPOSITE])->one();

        $this->compositeAttributes = $pricingAttributes = $this->service->getPricingAttributes($priceType, $this->priceGroupID);

        $this->attributeGroups = $pricingAttributesGroup = collect($pricingAttributes)
            ->groupBy('attribute_name')
            ->toArray();

        $this->serviceAttributes = collect($pricingAttributes)->pluck('service_attribute_id')->unique()->toArray();

        if (count($pricingAttributes) === 0) {
            $this->matrixHeaders = [];
            $this->matrixRows = [];
            return false;
        }

        $this->matrixHeaders = array_keys($pricingAttributesGroup);

        $this->groups = array_values($pricingAttributesGroup);
    }

    /**
     * Create No impact Row
     */
    private function configureNoImpactAttributes()
    {
        $priceType = PriceType::find()->where(['type' => PriceType::TYPE_NO_IMPACT])->one();

        $pricingAttributes = $this->service->getPricingAttributes($priceType, $this->priceGroupID);

        $pricingAttributesGroup = collect($pricingAttributes)->groupBy('attribute_name')->toArray();

        $attributes = collect($pricingAttributes)->pluck('service_attribute_id')->unique()->toArray();

        $this->serviceAttributes = array_merge($this->serviceAttributes, $attributes);

        if (count($pricingAttributesGroup) === 0) {
            $this->noImpactRows = [];
            return false;
        }

        $this->noImpactRows = $pricingAttributesGroup;
    }

    /**
     * @return bool
     */
    private function configureIndependentAttributes()
    {
        $priceType = PriceType::find()->where(['type' => PriceType::TYPE_INDEPENDENT])->one();

        $pricingAttributes = $this->service->getPricingAttributes($priceType, $this->priceGroupID);

        $pricingAttributesGroup = collect($pricingAttributes)->groupBy('attribute_name')->toArray();

        $attributes = collect($pricingAttributes)->pluck('service_attribute_id')->unique()->toArray();

        $this->serviceAttributes = array_merge($this->serviceAttributes, $attributes);

        if (count($pricingAttributesGroup) === 0) {
            $this->independentRows = [];
            return false;
        }

        $this->independentRows = $pricingAttributesGroup;
    }

    /**
     * Update incremental attribute
     */
    private function configureIncrementalAttributes()
    {
        $priceType = PriceType::find()->where(['type' => PriceType::TYPE_INCREMENTAL])->one();

        $pricingAttributes = $this->service->getPricingAttributes($priceType, $this->priceGroupID);

        $this->incrementalAttributes = collect($pricingAttributes)->groupBy('attribute_name')->keys()->toArray();
    }

    /**
     * Create Matrix
     * @return mixed
     */
    private function createMatrix()
    {
        if (count($this->attributeGroups) === 0) {
            return $this;
        }

        foreach ($this->groups as $index => $group) {
            $this->rounds[$index] = 0;
            $this->roundsLimits[$index] = count($group);
        }

        $firstGroupIndex = 0;
        $lastGroupIndex = count($this->getGroups()) - 1;
        $firstGroupLimit = $this->roundsLimits[$firstGroupIndex];

        $clockStates = [];

        while (true) {

            $clockStates[] = $this->rounds;

            $this->rounds[$lastGroupIndex] = $this->rounds[$lastGroupIndex] + 1;

            $this->incrementPrevious($firstGroupIndex);

            $firstGroupCount = $this->rounds[$firstGroupIndex];

            if ($firstGroupCount >= $firstGroupLimit) {
                break;
            }
        }

        foreach ($clockStates as $clockState) {
            $row = [];
            foreach ($clockState as $index => $value) {
                $group = $this->getGroup($index);
                $row[] = $group[$value];
            }
            $this->matrixRows[] = $row;
            $this->rowIdentifiers[] = $this->makeIdentifiers($row);
        }

        return $this;
    }

    /**
     * @param $lastIndex
     */
    private function incrementPrevious($lastIndex)
    {
        for ($i = count($this->rounds) - 1; $i >= 0; $i--) {

            $roundCount = $this->rounds[$i];
            $roundLimit = $this->roundsLimits[$i];

            if ($roundCount === $roundLimit) {

                $this->incrementBigger($i - 1);

                if ($i !== $lastIndex) {

                    $this->rounds[$i] = 0;
                }
            }
        }
    }

    /**
     * @param $index
     * @return null
     */
    private function incrementBigger($index)
    {
        if (!isset($this->rounds[$index])) {
            return null;
        }

        $this->rounds[$index] = $this->rounds[$index] + 1;
    }

    /**
     * Delete existing configuration
     */
    public function deleteExistingConfiguration()
    {
        $this->deleteExistingMatrix();
        $this->deleteExistingPrices();
        $this->deleteParents();
    }

    /**
     * save matrix rows
     */
    public function saveMatrixRows()
    {
        foreach ($this->getMatrixRows() as $row) {
            $this->saveMatrixRow($row);
        }
    }

    /**
     * Save a matrix row
     * @param $row
     * @return mixed
     */
    protected function saveMatrixRow($row)
    {
        if (empty($row)) {
            return false;
        }

        $data = [];
        $pricingAttributeParent = new PricingAttributeParent();
        $pricingAttributeParent->service_id = $this->service->id;
        $pricingAttributeParent->save();

        foreach ($row as $item) {
            $pricingAttributeMatrix = new PricingAttributeMatrix();
            $pricingAttributeMatrix->pricing_attribute_parent_id = $pricingAttributeParent->id;
            $pricingAttributeMatrix->service_attribute_option_id = $item['service_attribute_option_id'];
            $pricingAttributeMatrix->save();

            $data[] = $pricingAttributeMatrix;
        }

        return $data;
    }

    /**
     *  delete existing matrix
     */
    protected function deleteExistingMatrix()
    {
        $pricingAttributeParents = PricingAttributeParent::find()->where(['service_id' => $this->service->id])->all();

        /** @var PricingAttributeParent $pricingAttributeParent */
        foreach ($pricingAttributeParents as $pricingAttributeParent) {
            // delete matrix prices
            ProvidedServiceCompositePricing::deleteAll(['pricing_attribute_parent_id' => $pricingAttributeParent->id]);

            // delete matrix rows
            PricingAttributeMatrix::deleteAll(['pricing_attribute_parent_id' => $pricingAttributeParent->id]);
        }
    }

    /**
     * delete existing prices
     */
    protected function deleteExistingPrices()
    {
        $pricingAttributes = $this->service->getAllPricingAttributes();
        /** @var PricingAttribute $pricingAttribute */
        foreach ($pricingAttributes as $pricingAttribute) {
            // delete all base pricing
            ProvidedServiceIndependentPricing::deleteAll(['pricing_attribute_id' => $pricingAttribute->id]);
        }


        $pricingAttributeParents = PricingAttributeParent::find()->where(['service_id' => $this->service->id])->all();
        /** @var PricingAttributeParent $pricingAttributeParent */
        foreach ($pricingAttributeParents as $pricingAttributeParent) {
            // delete matrix prices
            ProvidedServiceCompositePricing::deleteAll(['pricing_attribute_parent_id' => $pricingAttributeParent->id]);
        }
    }

    /**
     * delete parents
     */
    protected function deleteParents()
    {
        $pricingAttributeParents = PricingAttributeParent::find()->where(['service_id' => $this->service->id])->all();

        /** @var PricingAttributeParent $pricingAttributeParent */
        foreach ($pricingAttributeParents as $pricingAttributeParent) {
            // delete parent
            $pricingAttributeParent->delete();
        }
    }

    /**
     * Delete area prices
     * @param $area_id
     */
    public function deleteAreaPrices($area_id)
    {
        $this->deleteIndependentPrices($area_id);
        $this->deleteNoImpactPrices($area_id);
        $this->deleteCompositePrices($area_id);
    }

    public function deleteIndependentPrices($area_id)
    {
        $priceType = PriceType::find()->where(['type' => PriceType::TYPE_INDEPENDENT])->one();

        if (!$priceType) {
            return false;
        }

        $pricingAttributes = $this->service->getPricingAttributes($priceType);

        if (empty($pricingAttributes)) {
            return false;
        }

        /** @var PricingAttribute $pricingAttribute */
        foreach ($pricingAttributes as $pricingAttribute) {

            if (!in_array($pricingAttribute['service_attribute_id'], $this->serviceAttributes)) {
                continue;
            }

            // delete all base pricing of this group only
            ProvidedServiceIndependentPricing::deleteAll([
                'pricing_attribute_id' => $pricingAttribute['pricing_attribute_id'],
                'provided_service_area_id' => $area_id
            ]);
        }

        return true;
    }

    public function deleteNoImpactPrices($area_id)
    {
        $priceType = PriceType::find()->where(['type' => PriceType::TYPE_NO_IMPACT])->one();

        if (!$priceType) {
            return false;
        }

        $pricingAttributes = $this->service->getPricingAttributes($priceType);

        if (empty($pricingAttributes)) {
            return false;
        }

        /** @var PricingAttribute $pricingAttribute */
        foreach ($pricingAttributes as $pricingAttribute) {

            if (!in_array($pricingAttribute['service_attribute_id'], $this->serviceAttributes)) {
                continue;
            }

            // delete all base pricing of this group only
            ProvidedServiceNoImpactPricing::deleteAll([
                'pricing_attribute_id' => $pricingAttribute['pricing_attribute_id'],
                'provided_service_area_id' => $area_id
            ]);
        }

        return true;
    }

    public function deleteCompositePrices($area_id)
    {
        $pricingAttributeParents = PricingAttributeParent::find()->where(['service_id' => $this->service->id])->all();

        if (empty($pricingAttributeParents)) {
            return false;
        }


        /** @var PricingAttributeParent $pricingAttributeParent */
        foreach ($pricingAttributeParents as $pricingAttributeParent) {

            // if this attribute doesn't belongs to current matrix.
            if (!$this->hasIdentifier($pricingAttributeParent->getOptionIdsFormattedName())) {
                continue;
            }


            // delete matrices prices for this matrix
            ProvidedServiceCompositePricing::deleteAll([
                'pricing_attribute_parent_id' => $pricingAttributeParent->id,
                'provided_service_area_id' => $area_id
            ]);
        }

        return true;
    }

    /**
     * get row options
     *
     * @param $row
     * @return string
     */
    public static function getRowOptions($row)
    {
        return implode('_', static::getRowOptionsArray($row));
    }

    /**
     * get row options as array
     * @param $row
     * @return array
     */
    public static function getRowOptionsArray($row)
    {
        return array_column($row, 'service_attribute_option_id');
    }

    /**
     * make identifiers of the matrix
     * @param $row
     * @return string
     */
    public function makeIdentifiers($row)
    {
        $ids = collect($row)->pluck('service_attribute_option_id')->toArray();

        return implode('_', $ids);
    }

    /**
     * check if this matrix has the identifier
     * @param $identifier
     * @return bool
     */
    public function hasIdentifier($identifier)
    {
        if (count($this->matrixRows) === 0) {
            $this->createMatrix();
        }

        return in_array($identifier, $this->rowIdentifiers);
    }

    /**
     * create has for the matrix.
     */
    public function createHash()
    {
        if (empty($this->rowIdentifiers)) {
            $this->rowIdentifiers = $this->serviceAttributes;
        }
        $this->hash = md5(implode('-', $this->rowIdentifiers));
    }

    /**
     * getter for matrix hash
     * @return mixed
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Match the incoming hash with the matrix hash
     *
     * @param $hash
     * @return bool
     */
    public function isEqual($hash)
    {
        return $hash === $this->hash;
    }

    /**
     * @return array
     */
    private function getGroups()
    {
        return $this->groups;
    }

    /**
     * @return array
     */
    public function getNoImpactRows()
    {
        return $this->noImpactRows;
    }

    /**
     * @return array
     */
    public function getIndependentRows()
    {
        return $this->independentRows;
    }

    /**
     * @param $index
     * @return mixed|null
     */
    private function getGroup($index)
    {
        if (isset($this->groups[$index])) {
            return $this->groups[$index];
        }

        return null;
    }

    /**
     * @return array
     */
    public function getMatrixHeaders()
    {
        return $this->matrixHeaders;
    }

    /**
     * @return array
     */
    public function getMatrixRows()
    {
        return $this->matrixRows;
    }

    /**
     * @return array
     */
    public function getCompositeAttributes()
    {
        return $this->compositeAttributes;
    }

    /**
     * @return array
     */
    public function getIncrementalAttributes()
    {
        return $this->incrementalAttributes;
    }

    /**
     * @return Collection
     */
    public function getAttributesGroup()
    {
        return $this->attributeGroups;
    }
}