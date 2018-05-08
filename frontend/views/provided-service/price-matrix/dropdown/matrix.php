<?php

use common\models\ProvidedService;
use common\models\ProvidedServiceArea;
use RummyKhan\Collection\Arr;
use yii\web\View;
use kartik\select2\Select2;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $attributeGroups array */
/* @var $incremental array */
/* @var $matrixHeaders array */
/* @var $matrixRows array */
/* @var $model ProvidedService */
/* @var $type string */
/** @var $area ProvidedServiceArea */

$columns = count($attributeGroups) > 0 ? intval(9 / count($attributeGroups)) : 0;

?>

<?php if (count($attributeGroups) > 0) { ?>

    <?php if (!empty($incremental)) { ?>
        <div class="row">
            <div class="col-md-12">
                Price by <b><?= implode(',', $incremental) ?></b>
            </div>
        </div>
    <?php } ?>

    <?php ActiveForm::begin([
        'action' => [
            '/provided-service/set-dropdown-pricing',
            'id' => $model->id,
            'area' => $area->id,
            'type' => $type
        ],
        'method' => 'POST'
    ]) ?>

    <div class="row">

        <?php foreach ($attributeGroups as $title => $attributeGroup) { ?>
            <div class="col-md-<?= $columns ?>">
                <div class="form-group">
                    <label for=""><?= $title ?></label>
                    <?= Select2::widget([
                        'name' => 'attribute[]',
                        'data' => collect($attributeGroup)->pluck('attribute_option_name', 'service_attribute_option_id')->toArray(),
                        'options' => ['placeholder' => 'Select ' . strtolower($title)]
                    ]) ?>
                    <span class="help-block">* Required</span>
                </div>
            </div>
        <?php } ?>

        <div class="col-md-2">
            <div class="form-group">
                <label for="">Price</label>
                <input type="number" name="price" class="form-control" required>
                <span class="help-block">* Required</span>
            </div>
        </div>

        <div class="col-md-1">
            <div class="form-group">
                <br>
                <button class="btn btn-primary">
                    Save
                </button>
            </div>
        </div>

    </div>

    <?php ActiveForm::end() ?>

<?php } ?>