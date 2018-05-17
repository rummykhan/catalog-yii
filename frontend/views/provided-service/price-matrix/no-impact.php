<?php

use common\models\ProvidedService;
use common\models\ProvidedServiceArea;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $matrix \common\helpers\Matrix */
/* @var $model ProvidedService */
/* @var $area ProvidedServiceArea */
/* @var $type string */

?>

<?php if (count($matrix->getNoImpactRows()) > 0) { ?>

    <div class="row">
        <div class="col-md-6">

            <?php foreach ($matrix->getNoImpactRows() as $title => $noImpactSingleRow) { ?>
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th><?= $title ?></th>
                        <th>Enabled</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($noImpactSingleRow as $item => $value) { ?>
                        <tr>
                            <td>
                                <?= $value['attribute_option_name'] ?>
                            </td>
                            <td class="text-center">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <input name="no_impact_price[<?= $value['service_attribute_option_id'] ?>]"
                                               type="checkbox"
                                            <?= $model->isNoImpactOptionEnabled($area->id, $value['service_attribute_option_id']) ? 'checked="checked"' : '' ?>>
                                    </span>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            <?php } ?>


        </div>
    </div>

<?php } ?>