<?php

use yii\helpers\Html;
use kartik\select2\Select2;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \common\models\Service */
/* @var $matrixHeaders array */
/* @var $matrixRows array */
/* @var $noImpactRows array */
/* @var $independentRows array */

$this->title = 'Price matrix';
$this->params['breadcrumbs'][] = ['label' => 'Services', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['/service/view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php if (count($matrixRows) > 0) { ?>
    <div class="row">
        <div class="col-md-3">
            <h4>Composite attributes</h4>
            <hr>
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
                <?php foreach ($row as $column) { ?>
                    <td><?= $column['attribute_option_name'] ?></td>
                <?php } ?>
                <td>
                    <input type="text" disabled>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
<?php } ?>

<?php if (count($independentRows) > 0) { ?>
    <div class="row">
        <div class="col-md-3">
            <h4>Independent attributes</h4>
            <hr>
        </div>
    </div>

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
                    <td><input type="text" disabled></td>
                <?php } ?>
            </tr>
            </tbody>
        </table>
    <?php } ?>

<?php } ?>


<?php if (count($noImpactRows) > 0) { ?>

    <div class="row">
        <div class="col-md-3">
            <h4>No impact attributes</h4>
            <hr>
        </div>
    </div>

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

<?php } ?>


<?php ActiveForm::begin(['action' => ['/service/confirm-price-matrix', 'id' => $model->id],
    'method' => 'POST']) ?>

<button class="btn btn-primary pull-right">Confirm Price Matrix</button>

<?php ActiveForm::end() ?>

