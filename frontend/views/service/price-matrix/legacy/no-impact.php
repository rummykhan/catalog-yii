<?php

use yii\web\View;

/* @var $this View */
/* @var $noImpactRows array */

?>


<?php if (count($noImpactRows) > 0) { ?>

    <?php foreach ($noImpactRows as $title => $noImpactSingleRow) { ?>
        <h4><?= $title ?></h4>
        <table class="table table-bordered">
            <tbody>
            <tr>
                <?php foreach ($noImpactSingleRow as $item => $value) { ?>
                    <td class="text-center"><?= $value['attribute_option_name'] ?></td>
                <?php } ?>
            </tr>
            </tbody>
        </table>
    <?php } ?>
<?php } ?>