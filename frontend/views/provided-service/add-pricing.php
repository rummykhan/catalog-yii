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
/** @var $area \common\models\ProvidedServiceArea */
/** @var $providedServiceType \common\models\ProvidedServiceType */


$this->title = 'Add Pricing for ' . $area->name;
$this->params['breadcrumbs'][] = ['label' => $provider->username, 'url' => ['/provider/view', 'id' => $model->provider_id]];
$this->params['breadcrumbs'][] = ['label' => 'Provided Services', 'url' => ['/provided-service/index', 'provider_id' => $model->provider_id]];
$this->params['breadcrumbs'][] = ['label' => $service->name, 'url' => ['/provided-service/view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = ['label' => 'Coverage Areas', 'url' => ['/provided-service/view-coverage-areas', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;

?>

<?php ActiveForm::begin([
    'action' => [
        '/provided-service/add-pricing',
        'id' => $model->id,
        'area' => $area->id,
        'type' => $providedServiceType->service_type_id
    ],
    'method' => 'POST'
]) ?>

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
                <input type="text" name="matrix_price[<?= implode('_', $matrixItems) ?>]" value="<?= $model->getPriceOfMatrixRow($matrixItems) ?>" class="form-control">
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>

<?php foreach ($noImpactRows as $title => $noImpactSingleRow) { ?>
    <table class="table table-striped">
        <thead>
        <tr>
            <th></th>
            <?php foreach ($noImpactSingleRow as $item => $column) { ?>
                <th><?= $column['attribute_option_name'] ?></th>
            <?php } ?>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>Pricing</td>
            <?php foreach ($noImpactSingleRow as $item => $column) { ?>
                <td><input type="text" name="no_impact_price[<?= $column['service_attribute_option_id'] ?>]"></td>
            <?php } ?>
        </tr>
        </tbody>
    </table>
<?php } ?>

<button class="btn btn-primary pull-right">Save Pricing</button>
<?php ActiveForm::end() ?>
