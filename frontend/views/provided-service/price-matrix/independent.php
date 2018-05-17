<?php

use common\models\ProvidedService;
use common\models\ProvidedServiceArea;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $matrix \common\helpers\Matrix */
/* @var $this View */
/* @var $model ProvidedService */
/* @var $area ProvidedServiceArea */
/* @var $type string */

?>

<?php if (count($matrix->getIndependentRows()) > 0) { ?>

    <div class="row">
        <div class="col-md-6">

            <?php foreach ($matrix->getIndependentRows() as $title => $independentRow) { ?>
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th><?= $title ?></th>
                        <th>Pricing</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($independentRow as $item => $column) { ?>
                        <tr>
                            <th><?= $column['attribute_option_name'] ?></th>
                            <td>
                                <div class="input-group">
                            <span class="input-group-addon">
                              <input type="checkbox" class="disable-input"
                                  <?= $model->getPriceOfIndependentRow($column['service_attribute_option_id'], $area->id) ? 'checked="checked"' : '' ?> >
                            </span>
                                    <input
                                            type="number"
                                            class="form-control"
                                            name="independent_price[<?= $column['service_attribute_option_id'] ?>]"
                                            value="<?= $model->getPriceOfIndependentRow($column['service_attribute_option_id'], $area->id) ?>"
                                    >
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
