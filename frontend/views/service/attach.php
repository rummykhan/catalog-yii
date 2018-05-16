<?php

use yii\helpers\Html;
use kartik\select2\Select2;
use yii\widgets\ActiveForm;
use common\helpers\MultilingualInputHelper;

/* @var $this yii\web\View */
/* @var $service common\models\Service */
/* @var $model \common\forms\AttachAttribute */


$this->title = 'Add Field';
$this->params['breadcrumbs'][] = ['label' => 'Services', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $service->name, 'url' => ['/service/view', 'id' => $service->id]];
$this->params['breadcrumbs'][] = $this->title;

?>

<?php $form = ActiveForm::begin([
    'action' => ['/service/attach-attribute', 'id' => $service->id]
]) ?>

    <div class="row">
        <div class="col-md-6">

            <?= MultilingualInputHelper::textInputs($form, $model, 'attribute_name') ?>

            <?= MultilingualInputHelper::textareaInputs($form, $model, 'description') ?>

            <?= MultilingualInputHelper::textareaInputs($form, $model, 'mobile_description') ?>

        </div>

        <div class="col-md-6">

            <?= $form->field($model, 'icon')->fileInput() ?>

            <div class="form-group">
                <label for="">Field Type</label>
                <?= Select2::widget([
                    'model' => $model,
                    'attribute' => 'field_type',
                    'data' => \frontend\helpers\FieldsConfigurationHelper::getDropDownData(),
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
                <span class="help-block">
                    <b>Composite</b> type will share price with another attribute.
                    <br>
                    <b>Incremental</b> type will multiply the selected quantity to composite price.
                    <br>
                    <b>No impact</b> type is just instructional for provider, no prices.
                    <br>
                    <b>Independent</b> type will have individual price for all of it's options.
                </span>
            </div>

            <div class="form-group">
                <label for="">Validations</label>
                <?= Select2::widget([
                    'model' => $model,
                    'attribute' => 'validations',
                    'data' => \common\models\Validation::toList(),
                    'value' => $model->validations,
                    'options' => ['placeholder' => 'Select validations'],
                    'pluginOptions' => [
                        'multiple' => true,
                        'allowClear' => true
                    ]
                ]) ?>
            </div>

        </div>
    </div>

<div class="row">
    <div class="col-md-12 text-right">
        <div class="form-group">
            <?= Html::submitButton('Add Field', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
</div>

<?php ActiveForm::end() ?>


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
var valueInputSelector = '#values';
var bulkInputSelector = '#bulk';

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

function hideValuesInput(){
    if(!$(valueInputSelector).hasClass('hidden')){
        $(valueInputSelector).addClass('hidden');
    }
}

function showValuesInput(){
    if($(valueInputSelector).hasClass('hidden')){
        $(valueInputSelector).removeClass('hidden');
    }
}

function hideBulk(){
    if(!$(bulkInputSelector).hasClass('hidden')){
        $(bulkInputSelector).addClass('hidden');
    }
}

function showBulk(){
    if($(bulkInputSelector).hasClass('hidden')){
        $(bulkInputSelector).removeClass('hidden');
    }
}

function hideAll(){
    hideRange();
    hideValuesInput();
    hideBulk();
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
    
    hideAll();
    
    if(!typeConfiguration.rangeHidden){
        showRange();
    }
    
    if(!typeConfiguration.valueHidden){
        showValuesInput();
    }
    
    if(!typeConfiguration.bulkHidden){
        showBulk();
    }
    
}

$(fieldTypeSelector).on('select2:select', function (e) {
   var data = e.params.data;
   loadConfiguration(data.id);
});

JS;

$this->registerJs($js);


?>