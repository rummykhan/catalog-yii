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


$this->title = 'Add Pricing';
$this->params['breadcrumbs'][] = ['label' => $provider->username, 'url' => ['/provider/view', 'id' => $model->provider_id]];
$this->params['breadcrumbs'][] = ['label' => 'Provided Services', 'url' => ['/provided-service/index', 'provider_id' => $model->provider_id]];
$this->params['breadcrumbs'][] = ['label' => $service->name, 'url' => ['/provided-service/view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;

?>

<?php ActiveForm::begin([
    'action' => ['/provided-service/add-pricing', 'id' => $model->id],
    'method' => 'POST'
]) ?>

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label for="">Select City</label>
            <?= Select2::widget([
                'name' => 'city',
                'data' => $service->getCitiesList(),
                'options' => ['placeholder' => 'Select city'],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                    'change' => new \yii\web\JsExpression( "function(e) { console.log('change'); }")
                ]
            ]) ?>
        </div>
    </div>
</div>

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
                <input type="text" name="matrix_price[<?= implode('_', $matrixItems) ?>]" class="form-control">
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
                <td><input type="text"></td>
            <?php } ?>
        </tr>
        </tbody>
    </table>
<?php } ?>

<button class="btn btn-primary pull-right">Save Pricing</button>
<?php ActiveForm::end() ?>
