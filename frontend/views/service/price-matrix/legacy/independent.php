<?php

use yii\web\View;

/* @var $this View */
/* @var $independentRows array */

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
                    <td><input type="text" class="form-control" disabled></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    <?php } ?>

<?php } ?>
