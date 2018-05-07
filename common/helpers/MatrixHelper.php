<?php
/**
 * Created by PhpStorm.
 * User: rummykhan
 * Date: 4/23/18
 * Time: 10:08 AM
 */

namespace common\helpers;


use common\models\PriceType;
use common\models\PricingAttributeMatrix;
use common\models\PricingAttributeParent;
use common\models\Service;

class MatrixHelper
{
    /**
     * @var Service $service
     */
    protected $service;

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
     * MatrixHelper constructor.
     * @param $service Service
     */
    public function __construct($service)
    {
        $this->service = $service;
        $this->createMatrix();
        $this->createNoImpactRows();
        $this->createIndependentRows();
    }

    /**
     * Create Matrix Clock using composite attributes
     */
    private function createMatrix()
    {
        $priceType = PriceType::find()->where(['type' => PriceType::TYPE_COMPOSITE])->one();

        $this->compositeAttributes = $pricingAttributes = $this->service->getPricingAttributes($priceType);

        // TODO: What if pricing attributes are empty
        $pricingAttributesGroup = collect($pricingAttributes)
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

        $pricingAttributes = $this->service->getPricingAttributes($priceType);

        $pricingAttributesGroup = collect($pricingAttributes)->groupBy('attribute_name')->toArray();

        if (count($pricingAttributesGroup) === 0) {
            $this->noImpactRows = [];
            return false;
        }

        $this->noImpactRows = $pricingAttributesGroup;
    }

    private function createIndependentRows()
    {
        $priceType = PriceType::find()->where(['type' => PriceType::TYPE_INDEPENDENT])->one();

        $pricingAttributes = $this->service->getPricingAttributes($priceType);

        $pricingAttributesGroup = collect($pricingAttributes)->groupBy('attribute_name')->toArray();

        if (count($pricingAttributesGroup) === 0) {
            $this->independentRows = [];
            return false;
        }

        $this->independentRows = $pricingAttributesGroup;
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

    public function getCompositeAttributes()
    {
        return $this->compositeAttributes;
    }

    public function saveMatrixRow($row)
    {
        if (empty($row)) {
            return false;
        }

        $pricingAttributeParent = new PricingAttributeParent();
        $pricingAttributeParent->service_id = $this->service->id;
        $pricingAttributeParent->save();

        foreach ($row as $item) {
            $pricingAttributeMatrix = new PricingAttributeMatrix();
            $pricingAttributeMatrix->pricing_attribute_parent_id = $pricingAttributeParent->id;
            $pricingAttributeMatrix->service_attribute_option_id = $item['service_attribute_option_id'];
            $pricingAttributeMatrix->save();
        }

        return true;

    }
}