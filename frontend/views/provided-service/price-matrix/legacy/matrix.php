<?php

/* @var $attributeGroups array */
use common\helpers\Matrix;

/* @var $incremental array */


?>

<?php

$css = <<<CSS

#price-matrix-table td {
    border: 1px solid black;
}

#price-matrix-table th {
    border: 1px solid black;
}

CSS;

$this->registerCss($css);

?>

<?php if (count($attributeGroups) === 2) { ?>
    <?php


    $groupNames = array_keys($attributeGroups);
    $groups = array_values($attributeGroups);

    $group1Name = $groupNames[0];
    $group2Name = $groupNames[1];

    $group1 = $groups[0];
    $group2 = $groups[1];

    if (count($group2) > count($group1)) {
        $swap = $group2;

        $group2 = $group1;
        $group1 = $swap;

        $swap = $group2Name;
        $group2Name = $group1Name;
        $group1Name = $swap;
    }

    ?>

    <p>
        Pricing by <b><?= implode(',', $incremental) ?></b>
    </p>
    <table class="table table-bordered" id="price-matrix-table">
        <tbody>
        <tr>
            <td colspan="2" rowspan="2">&nbsp;</td>
            <th colspan="<?= count($group2) ?>" class="text-center"><?= $group2Name ?></th>
        </tr>
        <tr>
            <?php foreach ($group2 as $item) { ?>
                <th class="text-center"><?= $item['attribute_option_name'] ?></th>
            <?php } ?>
        </tr>
        <tr>
            <th rowspan="<?= count($group1) + 1 ?>"><?= $group1Name ?></th>
        </tr>
        <?php foreach ($group1 as $item) { ?>
            <tr>
                <th><?= $item['attribute_option_name'] ?></th>
                <?php foreach ($group2 as $item2) { ?>
                    <td class="text-center">
                        <div class="input-group">
                            <span class="input-group-addon">
                              <input type="checkbox" class="disable-input">
                            </span>
                            <input type="number" class="form-control" name="matrix_price[<?= Matrix::getRowOptions([$item, $item2]) ?>]">
                        </div>
                    </td>
                <?php } ?>
            </tr>
        <?php } ?>
        </tbody>
    </table>
<?php }  ?>
