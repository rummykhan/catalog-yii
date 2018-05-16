<?php

use common\helpers\GlobalHelper;
use yii\helpers\Html;
use kartik\select2\Select2;
use yii\widgets\ActiveForm;
use common\helpers\MultilingualInputHelper;

/* @var $this yii\web\View */
/* @var $service common\models\Service */
/* @var $model \common\forms\UpdateAttribute */
/** @var $attribute \common\models\ServiceAttribute */


$this->title = 'Update Field';
$this->params['breadcrumbs'][] = ['label' => 'Services', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $service->name, 'url' => ['/service/view', 'id' => $service->id]];
$this->params['breadcrumbs'][] = $this->title;

?>

<?php $form = ActiveForm::begin([
    'action' => ['/service/edit-attribute', 'id' => $service->id, 'attribute_id' => $attribute->id]
]) ?>


<?php if (!empty($attribute->icon)) { ?>
    <div class="row">
        <div class="col-md-12 text-center">
            <img src="<?= $attribute->getImageFileUrl('icon') ?>" alt="">
        </div>
    </div>
<?php } ?>

<div class="row">
    <div class="col-md-6">

        <?= MultilingualInputHelper::textInputs($form, $model, 'name') ?>

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
                'value' => $model->field_type,
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
                'attribute' => 'input_type_id',
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
                'attribute' => 'user_input_type_id',
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
                'attribute' => 'price_type_id',
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
                'attribute' => 'attribute_validations',
                'data' => \common\models\Validation::toList(),
                'value' => $model->attribute_validations,
                'options' => ['placeholder' => 'Select validations'],
                'pluginOptions' => [
                    'multiple' => true,
                    'allowClear' => true
                ]
            ]) ?>
        </div>

    </div>
</div>


<div class="form-group">
    <?= Html::submitButton('Update Field', ['class' => 'btn btn-primary']) ?>
</div>


<?php ActiveForm::end() ?>


<?php

$fieldType = Html::getInputId($model, 'field_type');

$configuration = \frontend\helpers\FieldsConfigurationHelper::get();

$baseSelection = $model->field_type;

$js = <<<JS

var baseConfiguration = JSON.parse('$configuration');

var baseSelection = '{$baseSelection}';

var fieldTypeSelector = '#{$fieldType}';
var typeListSelector = '#type-list';
var typeRangeSelector = '#type-range';
var typeListParentSelector= '#list-type-parent';
var typeRangeParentSelector = '#range-type-parent';

var rangeTypeParentRangeSelector = '#range-type-parent-range';
var listTypeParentListSelector = '#list-type-parent-list';

var bulkInputSelector = '#bulk';

function removeOption(element){
    var target = element.attr('data-target');
    var targetParent = target+'-parent';
    
    $(target).attr('value', '');
    $(targetParent).addClass('hidden');
}

$('.remove-option').click(function(e){
    e.preventDefault();
   removeOption($(this)); 
});

function getConfiguration(type){
    
    if(!baseConfiguration[type]){
        return null;
    }
    return baseConfiguration[type]; 
}

function hideAll(){
    
    if(!$(typeListParentSelector).hasClass('hidden')){
        $(typeListParentSelector).addClass('hidden');
    }
    
    if(!$(typeRangeParentSelector).hasClass('hidden')){
        $(typeRangeParentSelector).addClass('hidden');
    }
    
    if(!$(bulkInputSelector).hasClass('hidden')){
        $(bulkInputSelector).addClass('hidden');
    }
}

function loadConfiguration(type) {
  var typeConfiguration = getConfiguration(type);
    
    if(!typeConfiguration){
        return false;
    }
    
    console.log('typeConfiguration', typeConfiguration);
    
    hideAll();
    
    
    if(!typeConfiguration.rangeHidden){
        $(typeRangeParentSelector).removeClass('hidden');
    }
    
    if(!typeConfiguration.valueHidden){
        $(typeListParentSelector).removeClass('hidden');
    }
    
    if(!typeConfiguration.bulkHidden){
        $(bulkInputSelector).removeClass('hidden');
    }
    
    if(typeConfiguration.rangeHidden && typeConfiguration.valueHidden && typeConfiguration.bulkHidden){
        return true;
    }
    
    if(baseSelection === 'range'){
        
        $(typeRangeParentSelector).removeClass('hidden');
        
        if(type === 'list'){
             $(typeListSelector).removeClass('hidden');
             $(rangeTypeParentRangeSelector).addClass('hidden');
        }else if (type === 'range'){
            $(typeListSelector).addClass('hidden');
            $(rangeTypeParentRangeSelector).removeClass('hidden');
        }
        
    }else if(baseSelection === 'list'){
        $(typeListParentSelector).removeClass('hidden');
        
        if (type === 'range'){
            $(typeRangeSelector).removeClass('hidden');
            $(listTypeParentListSelector).addClass('hidden');
        }else if(type === 'list'){
            $(typeRangeSelector).addClass('hidden');
            $(listTypeParentListSelector).removeClass('hidden');
        }
    }
    
}

$(fieldTypeSelector).on('select2:select', function (e) {
   var data = e.params.data;
   loadConfiguration(data.id);
});

JS;

$this->registerJs($js);

?>
