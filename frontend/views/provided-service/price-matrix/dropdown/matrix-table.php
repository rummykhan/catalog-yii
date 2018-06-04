<?php

use common\models\ProvidedService;
use common\models\ProvidedServiceArea;
use RummyKhan\Collection\Arr;
use yii\web\View;
use kartik\select2\Select2;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model ProvidedService */
/* @var $type string */
/* @var $area ProvidedServiceArea */
/* @var $matrix \common\helpers\Matrix */

$attributeGroups = $matrix->getAttributesGroup();

$incremental = $matrix->getIncrementalAttributes();

$columns = count($attributeGroups) > 0 ? intval(9 / count($attributeGroups)) : 0;

?>

<?php if (count($area->providedServiceCompositePricings) > 0) { ?>

    <div class="row">
        <div class="col-md-12">
            <table class="table table-hover" id="price-table">
                <thead>
                <tr>
                    <?php foreach ($attributeGroups as $title => $attributeGroup) { ?>
                        <th><?= $title ?></th>
                    <?php } ?>
                    <th>Price</th>
                </tr>
                </thead>
                <tbody>

                <?php foreach ($area->providedServiceCompositePricings as $pricing) { ?>
                    <?php if (!$matrix->hasIdentifier($pricing->pricingAttributeParent->getOptionIdsFormattedName())) {
                        continue;
                    } ?>
                    <tr>
                        <?php foreach ($pricing->pricingAttributeParent->pricingAttributeMatrices as $pricingAttributeMatrix) { ?>
                            <td><?= $pricingAttributeMatrix->serviceAttributeOption->name ?></td>
                        <?php } ?>
                        <td>
                            <div class="input-group">
                                <span class="input-group-addon">
                                  <input type="checkbox" class="disable-input" checked="checked">
                                </span>
                                <input type="number"
                                       class="form-control"
                                       name="matrix_price[<?= $pricing->pricingAttributeParent->getOptionIdsFormattedName() ?>]"
                                       value="<?= $pricing->price ?>">
                            </div>
                        </td>
                    </tr>
                <?php } ?>

                </tbody>
            </table>
        </div>
    </div>

<?php } ?>


