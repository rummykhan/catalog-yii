<?php

use common\helpers\ServiceAttributeMatrix;
use common\models\ProvidedService;
use common\models\ProvidedServiceArea;
use common\models\ProvidedRequestType;
use common\models\Provider;
use common\models\Service;
use frontend\assets\DataTableAsset;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $view int */
/* @var $model ProvidedService */
/* @var $provider Provider */
/* @var $service Service */
/** @var $area ProvidedServiceArea */
/** @var $providedServiceType ProvidedRequestType */
/* @var $motherMatrix ServiceAttributeMatrix */

DataTableAsset::register($this);

$this->title = 'Add Pricing for ' . $area->name;
$this->params['breadcrumbs'][] = ['label' => $provider->username, 'url' => ['/provider/view', 'id' => $model->provider_id]];
$this->params['breadcrumbs'][] = ['label' => 'Provided Services', 'url' => ['/provided-service/index', 'provider_id' => $model->provider_id]];
$this->params['breadcrumbs'][] = ['label' => $service->name, 'url' => ['/provided-service/view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = ['label' => $providedServiceType->serviceType->type];
$this->params['breadcrumbs'][] = ['label' => 'Coverage Areas', 'url' => ['/provided-service/view-coverage-areas', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;


$type = $providedServiceType->id;

?>

<?php foreach ($motherMatrix->getMatrices() as $index => $matrix) { ?>

    <?= $this->render('price-matrix/dropdown/matrix', compact('matrix', 'model', 'area', 'type', 'matrix')) ?>

    <?php ActiveForm::begin([
        'action' => [
            '/provided-service/set-pricing',
            'id' => $model->id,
            'area' => $area->id,
            'type' => $type,
            'hash' => $matrix->getHash(),
        ],
        'method' => 'POST',
        'options' => ['name' => md5(uniqid('f-', true))]
    ]) ?>

    <?= $this->render('price-matrix/dropdown/matrix-table', compact('matrix', 'area')) ?>

    <?= $this->render('price-matrix/independent', compact('matrix', 'model', 'area')) ?>

    <?= $this->render('price-matrix/no-impact', compact('matrix', 'model', 'area')) ?>

    <div class="row">
        <div class="col-md-6 text-right">
            <button class="btn btn-primary">Update</button>
        </div>
    </div>

    <?php ActiveForm::end() ?>

<?php } ?>


<?php

$js = <<<JS

function getTextBox(element){
    return element.find('input[type="number"]');
}

function enablePriceBox(checkBox){
    var textBox = getTextBox(checkBox.closest('.input-group'));
    
    textBox.removeAttr('disabled');
}

function disablePriceBox(checkBox){
    var textBox = getTextBox(checkBox.closest('.input-group'));
    
    textBox.attr('disabled', 'disabled');
}

function applySelection(element){
    if(element.is(':checked')){
        enablePriceBox(element);
    }else{
        disablePriceBox(element);
    }
}

$('.disable-input').click(function(e){
    applySelection($(this));
});


$.each($('.disable-input'), function(i, element){
    applySelection($(element));
})

JS;

$this->registerJs($js);

?>

<?php

$js = <<<JS

$(document).ready( function () {
    $('#price-table').DataTable();
} );

JS;

$this->registerJs($js);

?>