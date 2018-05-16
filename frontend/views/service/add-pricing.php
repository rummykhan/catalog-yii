<?php

use common\helpers\ServiceAttributeMatrix;
use yii\helpers\Html;
use kartik\select2\Select2;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \common\models\Service */
/**@var $motherMatrix ServiceAttributeMatrix */

$this->title = 'Price matrix';
$this->params['breadcrumbs'][] = ['label' => 'Services', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['/service/view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php foreach ($motherMatrix->getMatrices() as $index => $matrix) { ?>

    <?= $this->render('price-matrix', [
        'matrixHeaders' => $matrix->getMatrixHeaders(),
        'matrixRows' => $matrix->getMatrixRows(),
        'noImpactRows' => $matrix->getNoImpactRows(),
        'independentRows' => $matrix->getIndependentRows(),
        'incremental' => $matrix->getIncrementalAttributes(),
        'attributeGroups' => $matrix->getAttributesGroup()
    ]) ?>
    <hr>
<?php } ?>


<?php ActiveForm::begin([
    'action' => ['/service/confirm-price-matrix', 'id' => $model->id], 'method' => 'POST'
]) ?>


<button class="btn btn-primary pull-right">Confirm Price Matrix</button>

<?php ActiveForm::end() ?>

