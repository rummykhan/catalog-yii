<?php

use yii\web\View;
use kartik\select2\Select2;

/* @var $this View */
/* @var $attributeGroups array */
/* @var $incremental array */
/* @var $matrixHeaders array */
/* @var $matrixRows array */

$columns = count($attributeGroups) > 0 ? intval(10 / count($attributeGroups)) : 0;

?>

<?php if (count($attributeGroups) > 0) { ?>



    <div class="row">
        <?php foreach ($attributeGroups as $title => $attributeGroup) { ?>
            <div class="col-md-<?= $columns ?>">
                <div class="form-group">
                    <label for="">
                        <?= $title ?>
                    </label>
                    <?= Select2::widget([
                        'name' => $title,
                        'data' => collect($attributeGroup)->pluck('attribute_option_name', 'service_attribute_option_id')->toArray()
                    ]) ?>
                </div>
            </div>
        <?php } ?>
        <div class="col-md-2">
            <div class="form-group">
                <label for="">
                    Price
                    <?php if (!empty($incremental)) { ?>
                        by <b><?= implode(',', $incremental) ?></b>
                    <?php } ?>
                </label>
                <input type="text" class="form-control" disabled="disabled">
            </div>
        </div>
    </div>

<?php } ?>