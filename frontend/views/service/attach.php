<?php

use yii\helpers\Html;
use kartik\select2\Select2;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $service common\models\Service */
/* @var $model \common\forms\AttachAttribute */


$this->title = 'Add Fields';
$this->params['breadcrumbs'][] = ['label' => 'Services', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $service->name, 'url' => ['/service/view', 'id' => $service->id]];
$this->params['breadcrumbs'][] = $this->title;

?>

    <div class="row">
        <div class="col-md-6">
            <?php $form = ActiveForm::begin([
                'action' => ['/service/attach-attribute', 'id' => $service->id]
            ]) ?>

            <?= $form->field($model, 'attribute_name')->textInput() ?>

            <div class="form-group">
                <label for="">Field Type</label>
                <?= Select2::widget([
                    'model' => $model,
                    'attribute' => 'field_type',
                    'data' => [
                        'text' => 'Text',
                        'range' => 'Range',
                        'list' => 'List',
                        'toggle' => 'Boolean',
                        'file' => 'File',
                        'location' => 'Google Map',
                    ],
                    'options' => ['placeholder' => 'Select field type'],
                    'pluginOptions' => [
                        'multiple' => false,
                        'allowClear' => true,
                    ]
                ]) ?>
            </div>

            <div class="form-group">
                <label for="">Input type to be rendered</label>
                <?= Select2::widget([
                    'model' => $model,
                    'attribute' => 'input_type',
                    'data' => \common\models\InputType::toList(),
                    'options' => ['placeholder' => 'Select type of input'],
                    'pluginOptions' => [
                        'multiple' => false,
                        'allowClear' => true
                    ]
                ]) ?>
            </div>

            <div class="form-group">
                <label for="">User input</label>
                <?= Select2::widget([
                    'model' => $model,
                    'attribute' => 'user_input_type',
                    'data' => \common\models\UserInputType::toList(),
                    'options' => ['placeholder' => 'Select field to attach'],
                    'pluginOptions' => [
                        'multiple' => false,
                        'allowClear' => true
                    ]
                ]) ?>
            </div>

            <div class="form-group">
                <label for="">Price Type</label>
                <?= Select2::widget([
                    'model' => $model,
                    'attribute' => 'price_type',
                    'data' => \common\models\PriceType::toList(),
                    'options' => ['placeholder' => 'Select price type for this field'],
                    'pluginOptions' => [
                        'multiple' => false,
                        'allowClear' => true
                    ]
                ]) ?>
            </div>

            <div class="row hidden" id="range">
                <div class="col-md-6">
                    <?= $form->field($model, 'min')->textInput(['type' => 'number']) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'max')->textInput(['type' => 'number']) ?>
                </div>
            </div>

            <div class="form-group">
                <?= Html::submitButton('Add Field', ['class' => 'btn btn-primary']) ?>
            </div>


            <?php ActiveForm::end() ?>
        </div>
    </div>


<?php

$fieldTypeID = Html::getInputId($model, 'field_type');
$inputTypeID = Html::getInputId($model, 'input_type');
$userInputTypeID = Html::getInputId($model, 'user_input_type');
$priceTypeID = Html::getInputId($model, 'price_type');

$configuration = \frontend\helpers\FieldsConfigurationHelper::get();

$js = <<<JS

var baseConfiguration = JSON.parse('$configuration');

var fieldTypeSelector = '#{$fieldTypeID}';
var inputTypeSelector = '#{$inputTypeID}';
var userInputTypeSelector = '#{$userInputTypeID}';
var priceTypeSelector = '#{$priceTypeID}';
var rangeSelector = '#range'; 

function getConfiguration(type){
    
    if(!baseConfiguration[type]){
        return null;
    }
    return baseConfiguration[type]; 
}

function hideRange(){
    if(!$(rangeSelector).hasClass('hidden')){
        $(rangeSelector).addClass('hidden');
    }
}

function showRange(){
    if($(rangeSelector).hasClass('hidden')){
        $(rangeSelector).removeClass('hidden');
    }
}

function loadConfiguration(type){
    var typeConfiguration = getConfiguration(type);
    
    if(!type){
        return false;
    }
    
    console.log('typeConfiguration', typeConfiguration);
    
    $(inputTypeSelector).val(typeConfiguration.inputType.id).trigger('change');
    $(userInputTypeSelector).val(typeConfiguration.userInputType.id).trigger('change');
    $(priceTypeSelector).val(typeConfiguration.priceType.id).trigger('change');
    
    hideRange();
    
    if(!typeConfiguration.rangeHidden){
        showRange();
    }
    
}

$(fieldTypeSelector).on('select2:select', function (e) {
   var data = e.params.data;
   loadConfiguration(data.id);
});

JS;

$this->registerJs($js);


?>