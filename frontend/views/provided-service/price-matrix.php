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

?>

<?php if ($view == 1) { ?>
    <?php ActiveForm::begin([
        'action' => [
            '/provided-service/set-pricing',
            'id' => $model->id,
            'area' => $area->id,
            'type' => $type
        ],
        'method' => 'POST'
    ]) ?>

    <?= $this->render('price-matrix/basic/matrix', compact('matrixHeaders', 'matrixRows', 'incremental', 'model', 'area')) ?>
    <?= $this->render('price-matrix/independent', compact('independentRows')) ?>
    <?= $this->render('price-matrix/no-impact', compact('noImpactRows')) ?>

    <?php ActiveForm::end() ?>
<?php } else if ($view == 2) { ?>

    <?php ActiveForm::begin([
        'action' => [
            '/provided-service/set-pricing',
            'id' => $model->id,
            'area' => $area->id,
            'type' => $type
        ],
        'method' => 'POST'
    ]) ?>

    <?= $this->render('price-matrix/legacy/matrix', compact('attributeGroups', 'incremental', 'model', 'area')) ?>
    <?= $this->render('price-matrix/independent', compact('independentRows')) ?>
    <?= $this->render('price-matrix/no-impact', compact('noImpactRows')) ?>

    <?php ActiveForm::end() ?>

<?php } else if ($view == 3) { ?>
    <?= $this->render('price-matrix/dropdown/matrix', compact('attributeGroups', 'incremental', 'model', 'area', 'view', 'type')) ?>
    <?= $this->render('price-matrix/independent', compact('independentRows')) ?>
    <?= $this->render('price-matrix/no-impact', compact('noImpactRows')) ?>
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

if(in_array($view, range(1,2))){
    $this->registerJs($js);
}

?>
