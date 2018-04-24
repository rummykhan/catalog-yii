<?php

use yii\helpers\Html;
use kartik\select2\Select2;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \common\models\Service */
/* @var $matrixHeaders array */
/* @var $matrixRows array */
/* @var $noImpactRows array */

$this->title = 'Price matrix';
$this->params['breadcrumbs'][] = ['label' => 'Services', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['/service/view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<p>
    <a href="<?= \yii\helpers\Url::to(['/service/set-pricing', 'id' => $model->id]) ?>" class="btn btn-primary">Set
        Pricing Attributes</a>
</p>

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
                <input type="text">
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

