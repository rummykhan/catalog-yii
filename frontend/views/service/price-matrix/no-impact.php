<?php

use yii\web\View;

/* @var $this View */
/* @var $noImpactRows array */

?>


<?php if (count($noImpactRows) > 0) { ?>

    <?php foreach ($noImpactRows as $title => $noImpactSingleRow) { ?>
        <h4><?= $title ?></h4>
        <table class="table table-hover">
            <tbody>
            <?php foreach ($noImpactSingleRow as $item => $value) { ?>
                <tr>
                    <td class="text-center">
                        <?= $value['attribute_option_name'] ?>
                    </td>
                    <td>
                        <input type="text" class="form-control" disabled="disabled">
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    <?php } ?>
<?php } ?>