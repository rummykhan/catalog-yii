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

class ServiceAttributeMatrix
{
    /**
     * @var Service $service
     */
    protected $service;

    /**
     * @var Matrix[]
     */
    protected $matrices;

    /**
     * @var array|\common\models\PricingAttributeGroup[]
     */
    protected $priceGroups;

    /**
     * MatrixHelper constructor.
     * @param $service Service
     */
    public function __construct($service)
    {
        $this->service = $service;
        $this->priceGroups = !empty($this->service->pricingAttributeGroups) ? $this->service->pricingAttributeGroups : [];
        $this->noPriceGroupAttributes = $this->service->getPricingAttributesNotInGroup();

        foreach ($this->priceGroups as $pricingGroup) {
            $this->matrices[] = new Matrix($service, $pricingGroup->id);
        }

        if (!empty($this->noPriceGroupAttributes)) {
            $this->matrices[] = new Matrix($service);
        }
    }

    /**
     * @return Matrix[]
     */
    public function getMatrices()
    {
        return !empty($this->matrices) ? $this->matrices : [];
    }

    public function saveMatricesRows()
    {
        foreach ($this->getMatrices() as $matrix) {
            $matrix->deleteExistingConfiguration();
        }

        foreach ($this->getMatrices() as $matrix) {
            $matrix->saveMatrixRows();
        }
    }
}