<?php

use common\helpers\ServiceAttributeMatrix;
use common\models\ProvidedService;
use common\models\ProvidedServiceArea;
use common\models\ProvidedServiceType;
use common\models\Provider;
use common\models\Service;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $view int */
/* @var $model ProvidedService */
/* @var $provider Provider */
/* @var $service Service */
/* @var $type string */
/** @var $area ProvidedServiceArea */
/** @var $providedServiceType ProvidedServiceType */

/* @var $motherMatrix ServiceAttributeMatrix */
/* @var $attributeGroups array */
/* @var $matrixHeaders array */
/* @var $matrixRows array */
/* @var $noImpactRows array */
/* @var $independentRows array */
/* @var $incremental array */

?>

<div class="row">
    <div class="col-md-12">
        <a href="<?= Url::to([
            '/provided-service/set-pricing',
            'id' => $model->id,
            'area' => $area->id,
            'type' => $type,
            'view' => 1
        ]) ?>"
           class="btn <?= $view == 1 ? 'btn-primary' : 'btn-default' ?>">Basic</a>
        <a href="<?= Url::to([
            '/provided-service/set-pricing',
            'id' => $model->id,
            'area' => $area->id,
            'type' => $type,
            'view' => 2
        ]) ?>"
           class="btn <?= $view == 2 ? 'btn-primary' : 'btn-default' ?>">Legacy</a>
        <a href="<?= Url::to([
            '/provided-service/set-pricing',
            'id' => $model->id,
            'area' => $area->id,
            'type' => $type,
            'view' => 3
        ]) ?>"
           class="btn <?= $view == 3 ? 'btn-primary' : 'btn-default' ?>">Dropdown</a>
    </div>
</div>

<br>

<?php ActiveForm::begin([
    'action' => [
        '/provided-service/set-pricing',
        'id' => $model->id,
        'area' => $area->id,
        'type' => $providedServiceType->service_type_id
    ],
    'method' => 'POST'
]) ?>

<?php foreach ($motherMatrix->getMatrices() as $index => $matrix) { ?>
    <div class="panel panel-default">
        <div class="panel-heading">Group - <?= $index ?></div>
        <div class="panel-body">
            <?= $this->render('price-matrix', [
                'matrixHeaders' => $matrix->getMatrixHeaders(),
                'matrixRows' => $matrix->getMatrixRows(),
                'noImpactRows' => $matrix->getNoImpactRows(),
                'independentRows' => $matrix->getIndependentRows(),
                'incremental' => $matrix->getIncrementalAttributes(),
                'attributeGroups' => $matrix->getAttributesGroup(),
                'view' => $view
            ]) ?>
        </div>
    </div>
<?php } ?>

<button class="btn btn-primary pull-right">Save Pricing</button>
<?php ActiveForm::end() ?>
