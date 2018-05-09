<?php

use common\helpers\ServiceAttributeMatrix;
use yii\helpers\Html;
use kartik\select2\Select2;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \common\models\Service */
/**@var $motherMatrix ServiceAttributeMatrix */
/**@var $view int */

$this->title = 'Price matrix';
$this->params['breadcrumbs'][] = ['label' => 'Services', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['/service/view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>


<div class="row">
    <div class="col-md-12 text-right">
        <div>
            <b>Pricing Views: </b>
            <div class="btn-group">
                <a href="<?= \yii\helpers\Url::to(['/service/add-pricing', 'id' => $model->id, 'view' => 1]) ?>"
                   class="btn <?= $view == 1 ? 'btn-primary' : 'btn-default' ?>">Basic</a>
                <a href="<?= \yii\helpers\Url::to(['/service/add-pricing', 'id' => $model->id, 'view' => 2]) ?>"
                   class="btn <?= $view == 2 ? 'btn-primary' : 'btn-default' ?>">Legacy</a>
                <a href="<?= \yii\helpers\Url::to(['/service/add-pricing', 'id' => $model->id, 'view' => 3]) ?>"
                   class="btn <?= $view == 3 ? 'btn-primary' : 'btn-default' ?>">Dropdown</a>
            </div>
        </div>
    </div>
</div>

<br>

<?php foreach ($motherMatrix->getMatrices() as $index => $matrix) { ?>

    <?= $this->render('price-matrix', [
        'matrixHeaders' => $matrix->getMatrixHeaders(),
        'matrixRows' => $matrix->getMatrixRows(),
        'noImpactRows' => $matrix->getNoImpactRows(),
        'independentRows' => $matrix->getIndependentRows(),
        'incremental' => $matrix->getIncrementalAttributes(),
        'attributeGroups' => $matrix->getAttributesGroup(),
        'view' => $view
    ]) ?>
    <hr>
<?php } ?>


<?php ActiveForm::begin([
    'action' => ['/service/confirm-price-matrix', 'id' => $model->id], 'method' => 'POST'
]) ?>


<button class="btn btn-primary pull-right">Confirm Price Matrix</button>

<?php ActiveForm::end() ?>

