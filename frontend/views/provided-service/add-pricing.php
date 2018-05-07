<?php

use common\models\ServiceType;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model common\models\ProvidedService */
/* @var $provider common\models\Provider */
/* @var $service common\models\Service */
/* @var $type string */
/* @var $matrixHeaders array */
/* @var $matrixRows array */
/* @var $noImpactRows array */
/* @var $independentRows array */
/** @var $area \common\models\ProvidedServiceArea */
/** @var $providedServiceType \common\models\ProvidedServiceType */


$this->title = 'Add Pricing for ' . $area->name;
$this->params['breadcrumbs'][] = ['label' => $provider->username, 'url' => ['/provider/view', 'id' => $model->provider_id]];
$this->params['breadcrumbs'][] = ['label' => 'Provided Services', 'url' => ['/provided-service/index', 'provider_id' => $model->provider_id]];
$this->params['breadcrumbs'][] = ['label' => $service->name, 'url' => ['/provided-service/view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = ['label' => $providedServiceType->serviceType->type];
$this->params['breadcrumbs'][] = ['label' => 'Coverage Areas', 'url' => ['/provided-service/view-coverage-areas', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;

?>

<?php ActiveForm::begin([
    'action' => [
        '/provided-service/set-pricing',
        'id' => $model->id,
        'area' => $area->id,
        'type' => $providedServiceType->service_type_id
    ],
    'method' => 'POST'
]) ?>


<?php if (count($matrixRows) > 0) { ?>
    <div class="row">
        <div class="col-md-3">
            <h4>Composite attributes</h4>
            <hr>
        </div>
    </div>
<?php } ?>
<table class="table table-striped">
    <thead>
    <tr>
        <?php foreach ($matrixHeaders as $header) { ?>
            <th><?= $header ?></th>
        <?php } ?>
        <th>Pricing</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($matrixRows as $row) { ?>
        <tr>
            <?php $matrixItems = []; ?>
            <?php foreach ($row as $column) { ?>
                <td><?= $column['attribute_option_name'] ?></td>
                <?php $matrixItems[] = $column['service_attribute_option_id'] ?>
            <?php } ?>
            <td>
                <input type="text" name="matrix_price[<?= implode('_', $matrixItems) ?>]" value="<?= $model->getPriceOfMatrixRow($matrixItems, $area->id) ?>" class="form-control">
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>

<?php if (count($independentRows) > 0) { ?>
    <div class="row">
        <div class="col-md-3">
            <h4>Independent attributes</h4>
            <hr>
        </div>
    </div>
<?php } ?>

<?php foreach ($independentRows as $title => $independentRow) { ?>
    <table class="table table-striped">
        <thead>
        <tr>
            <th><?= $title ?></th>
            <?php foreach ($independentRow as $item => $column) { ?>
                <th><?= $column['attribute_option_name'] ?></th>
            <?php } ?>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>Pricing</td>
            <?php foreach ($independentRow as $item => $column) { ?>
                <td><input type="text" name="independent_price[<?= $column['service_attribute_option_id'] ?>]" value="<?= $model->getPriceOfIndependentRow($column['service_attribute_option_id'], $area->id) ?>"></td>
            <?php } ?>
        </tr>
        </tbody>
    </table>
<?php } ?>


<?php if (count($noImpactRows) > 0) { ?>
    <div class="row">
        <div class="col-md-3">
            <h4>No impact attributes</h4>
            <hr>
        </div>
    </div>
<?php } ?>

<?php foreach ($noImpactRows as $title => $noImpactSingleRow) { ?>
    <div class="row">
        <div class="col-md-4">
            <ul class="list-group">
                <b><?= $title ?></b>
                <?php foreach ($noImpactSingleRow as $item => $value) { ?>
                    <li class="list-group-item"><?= $value['attribute_option_name'] ?></li>
                <?php } ?>
            </ul>
        </div>
    </div>
<?php } ?>

<button class="btn btn-primary pull-right">Save Pricing</button>
<?php ActiveForm::end() ?>
