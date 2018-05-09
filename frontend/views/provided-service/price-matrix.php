<?php

use common\models\ProvidedService;
use common\models\ProvidedServiceArea;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $attributeGroups array */
/* @var $matrixHeaders array */
/* @var $matrixRows array */
/* @var $noImpactRows array */
/* @var $independentRows array */
/* @var $incremental array */
/* @var $view int */
/* @var $model ProvidedService */
/* @var $area ProvidedServiceArea */
/* @var $type string */
/* @var $matrix \common\helpers\Matrix */

?>

<?php if ($view == 1) { ?>
    <?php ActiveForm::begin([
        'action' => [
            '/provided-service/set-pricing',
            'id' => $model->id,
            'area' => $area->id,
            'type' => $type,
            'hash' => $matrix->getHash(),
        ],
        'method' => 'POST',
        'options' => [
            'name' => md5(uniqid('f-', true)),
        ]
    ]) ?>

    <?= $this->render('price-matrix/basic/matrix', compact('matrixHeaders', 'matrixRows', 'incremental', 'model', 'area')) ?>
    <?= $this->render('price-matrix/independent', compact('independentRows', 'model', 'area')) ?>
    <?= $this->render('price-matrix/no-impact', compact('noImpactRows')) ?>

    <div class="row">
        <div class="col-md-12 text-right">
            <button class="btn btn-primary">Update</button>
        </div>
    </div>

    <?php ActiveForm::end() ?>
<?php } else if ($view == 2) { ?>

    <?php ActiveForm::begin([
        'action' => [
            '/provided-service/set-pricing',
            'id' => $model->id,
            'area' => $area->id,
            'type' => $type,
            'hash' => $matrix->getHash(),
        ],
        'method' => 'POST',
        'options' => [
            'name' => md5(uniqid('f-', true)),
        ]
    ]) ?>

    <?= $this->render('price-matrix/legacy/matrix', compact('attributeGroups', 'incremental', 'model', 'area')) ?>
    <?= $this->render('price-matrix/independent', compact('independentRows', 'model', 'area')) ?>
    <?= $this->render('price-matrix/no-impact', compact('noImpactRows')) ?>

    <div class="row">
        <div class="col-md-12 text-right">
            <button class="btn btn-primary">Update</button>
        </div>
    </div>

    <?php ActiveForm::end() ?>

<?php } else if ($view == 3) { ?>
    <?= $this->render('price-matrix/dropdown/matrix', compact('attributeGroups', 'incremental', 'model', 'area', 'view', 'type', 'matrix')) ?>
    <?= $this->render('price-matrix/independent', compact('independentRows', 'model', 'area')) ?>
    <?= $this->render('price-matrix/no-impact', compact('noImpactRows')) ?>
<?php } ?>
