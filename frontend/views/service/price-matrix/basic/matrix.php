<?php

use yii\web\View;

/* @var $this View */
/* @var $matrixHeaders array */
/* @var $matrixRows array */
/* @var $incremental array */

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
                    <input type="text" class="form-control" disabled>
                </td>
                <?php if (!empty($incremental)) { ?>
                    <td>By <?= implode(',', $incremental) ?></td>
                <?php } ?>
            </tr>
        <?php } ?>
        </tbody>
    </table>
<?php } ?>
