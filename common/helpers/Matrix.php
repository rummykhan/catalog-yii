<?php

namespace common\helpers;


use common\models\PriceType;
use common\models\PricingAttribute;
use common\models\PricingAttributeGroup;
use common\models\PricingAttributeMatrix;
use common\models\PricingAttributeParent;
use common\models\ProvidedServiceBasePricing;
use common\models\ProvidedServiceMatrixPricing;
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
     * MatrixHelper constructor.
     * @param $service Service
     * @param $priceGroupID int
     */
    public function __construct($service, $priceGroupID = null)
    {
        $this->service = $service;
        $this->priceGroupID = $priceGroupID;

        $this->createMatrix();
        $this->createNoImpactRows();
        $this->createIndependentRows();
        $this->updateIncrementalAttribute();
    }

    /**
     * Create Matrix Clock using composite attributes
     */
    private function createMatrix()
    {
        $priceType = PriceType::find()->where(['type' => PriceType::TYPE_COMPOSITE])->one();

        $this->compositeAttributes = $pricingAttributes = $this->service->getPricingAttributes($priceType, $this->priceGroupID);

        $this->attributeGroups = $pricingAttributesGroup = collect($pricingAttributes)
            ->groupBy('attribute_name')
            ->toArray();

        if (count($pricingAttributes) === 0) {
            $this->matrixHeaders = [];
            $this->matrixRows = [];
            return false;
        }

        $this->matrixHeaders = array_keys($pricingAttributesGroup);

        $this->groups = array_values($pricingAttributesGroup);

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
        }
    }

    /**
     * Create No impact Row
     */
    private function createNoImpactRows()
    {
        $priceType = PriceType::find()->where(['type' => PriceType::TYPE_NO_IMPACT])->one();

        $pricingAttributes = $this->service->getPricingAttributes($priceType, $this->priceGroupID);

        $pricingAttributesGroup = collect($pricingAttributes)->groupBy('attribute_name')->toArray();

        if (count($pricingAttributesGroup) === 0) {
            $this->noImpactRows = [];
            return false;
        }

        $this->noImpactRows = $pricingAttributesGroup;
    }

    /**
     * @return bool
     */
    private function createIndependentRows()
    {
        $priceType = PriceType::find()->where(['type' => PriceType::TYPE_INDEPENDENT])->one();

        $pricingAttributes = $this->service->getPricingAttributes($priceType, $this->priceGroupID);

        $pricingAttributesGroup = collect($pricingAttributes)->groupBy('attribute_name')->toArray();

        if (count($pricingAttributesGroup) === 0) {
            $this->independentRows = [];
            return false;
        }

        $this->independentRows = $pricingAttributesGroup;
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

    /**
     * Save matrix rows
     */
    public function deleteExistingConfiguration()
    {
        $this->deleteExistingMatrix();
        $this->deleteExistingPrices();
        $this->deleteParents();
    }

    public function saveMatrixRows()
    {
        foreach ($this->getMatrixRows() as $row){
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

    protected function deleteExistingMatrix()
    {
        $pricingAttributeParents = PricingAttributeParent::find()->where(['service_id' => $this->service->id])->all();

        /** @var PricingAttributeParent $pricingAttributeParent */
        foreach ($pricingAttributeParents as $pricingAttributeParent) {
            // delete matrix prices
            ProvidedServiceMatrixPricing::deleteAll(['pricing_attribute_parent_id' => $pricingAttributeParent->id]);

            // delete matrix rows
            PricingAttributeMatrix::deleteAll(['pricing_attribute_parent_id' => $pricingAttributeParent->id]);
        }
    }

    protected function deleteExistingPrices()
    {
        $pricingAttributes = $this->service->getAllPricingAttributes();
        /** @var PricingAttribute $pricingAttribute */
        foreach ($pricingAttributes as $pricingAttribute) {
            // delete all base pricing
            ProvidedServiceBasePricing::deleteAll(['pricing_attribute_id' => $pricingAttribute->id]);
        }


        $pricingAttributeParents = PricingAttributeParent::find()->where(['service_id' => $this->service->id])->all();
        /** @var PricingAttributeParent $pricingAttributeParent */
        foreach ($pricingAttributeParents as $pricingAttributeParent) {
            // delete matrix prices
            ProvidedServiceMatrixPricing::deleteAll(['pricing_attribute_parent_id' => $pricingAttributeParent->id]);
        }
    }

    protected function deleteParents()
    {
        $pricingAttributeParents = PricingAttributeParent::find()->where(['service_id' => $this->service->id])->all();

        /** @var PricingAttributeParent $pricingAttributeParent */
        foreach ($pricingAttributeParents as $pricingAttributeParent) {
            // delete parent
            $pricingAttributeParent->delete();
        }
    }

    public function deleteAreaPrices($area_id)
    {
        $pricingAttributes = $this->service->getAllPricingAttributes();
        /** @var PricingAttribute $pricingAttribute */
        foreach ($pricingAttributes as $pricingAttribute) {
            // delete all base pricing
            ProvidedServiceBasePricing::deleteAll([
                'pricing_attribute_id' => $pricingAttribute->id,
                'provided_service_area_id' => $area_id
            ]);
        }


        $pricingAttributeParents = PricingAttributeParent::find()->where(['service_id' => $this->service->id])->all();
        /** @var PricingAttributeParent $pricingAttributeParent */
        foreach ($pricingAttributeParents as $pricingAttributeParent) {
            // delete matrix prices
            ProvidedServiceMatrixPricing::deleteAll([
                'pricing_attribute_parent_id' => $pricingAttributeParent->id,
                'provided_service_area_id' => $area_id
            ]);
        }
    }

    protected function updateIncrementalAttribute()
    {
        $priceType = PriceType::find()->where(['type' => PriceType::TYPE_INCREMENTAL])->one();

        $pricingAttributes = $this->service->getPricingAttributes($priceType, $this->priceGroupID);

        $this->incrementalAttributes = collect($pricingAttributes)->groupBy('attribute_name')->keys()->toArray();
    }

    public static function getRowOptions($row)
    {
        return implode('_', static::getRowOptionsArray($row));
    }

    public static function getRowOptionsArray($row)
    {
        return array_column($row, 'service_attribute_option_id');
    }
}