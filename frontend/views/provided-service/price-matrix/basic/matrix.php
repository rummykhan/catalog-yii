<?php

use common\helpers\Matrix;
use common\models\ProvidedService;
use common\models\ProvidedServiceArea;
use yii\web\View;

/* @var $this View */
/* @var $matrixHeaders array */
/* @var $matrixRows array */
/* @var $incremental array */
/* @var $model ProvidedService */
/* @var $area ProvidedServiceArea */

?>

<?php if (count($matrixRows) > 0) { ?>

    <table class="table table-bordered">
        <thead>
        <tr>
            <?php foreach ($matrixHeaders as $header) { ?>
                <th><?= $header ?></th>
            <?php } ?>

            <th>Pricing </th>

            <?php if (!empty($incremental)) { ?>
                <th>&nbsp;</th>
            <?php } ?>

        </tr>
        </thead>
        <tbody>
        <?php foreach ($matrixRows as $row) { ?>
            <tr>
                <?php foreach ($row as $column) { ?>
                    <td><?= $column['attribute_option_name'] ?></td>
                <?php } ?>
                <td>
                    <div class="input-group">
                        <span class="input-group-addon">
                          <input type="checkbox" class="disable-input" <?= $model->getPriceOfMatrixRow(Matrix::getRowOptionsArray($row), $area->id) ? 'checked="checked"' : '' ?> >
                        </span>
                        <input type="number"
                                class="form-control"
                               name="matrix_price[<?= Matrix::getRowOptions($row) ?>]"
                               value="<?= $model->getPriceOfMatrixRow(Matrix::getRowOptionsArray($row), $area->id) ?>">
                    </div>
                </td>
                <?php if (!empty($incremental)) { ?>
                    <td>By <?= implode(',', $incremental) ?></td>
                <?php } ?>
            </tr>
        <?php } ?>
        </tbody>
    </table>
<?php } ?>
