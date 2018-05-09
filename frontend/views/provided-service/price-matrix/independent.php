<?php

use common\models\ProvidedService;
use common\models\ProvidedServiceArea;
use yii\web\View;

/* @var $this View */
/* @var $independentRows array */
/* @var $model ProvidedService */
/* @var $area ProvidedServiceArea */

?>

<?php if (count($independentRows) > 0) { ?>

    <?php foreach ($independentRows as $title => $independentRow) { ?>
        <table class="table table-striped">
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

<?php } ?>
