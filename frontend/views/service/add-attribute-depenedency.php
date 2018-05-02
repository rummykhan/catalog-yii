<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use kartik\select2\Select2;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model \common\models\Service */
/* @var $options array */
/* @var $attribute_id integer */
/* @var $depends_on_id integer */

$this->title = 'Add Attribute Dependency';
$this->params['breadcrumbs'][] = ['label' => 'Services', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['/service/view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;

?>

<?php ActiveForm::begin(['action' => ['/service/add-attribute-dependency', 'id' => $model->id], 'method' => 'POST']) ?>

<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label>Field</label>
            <?= Select2::widget([
                'name' => 'attribute_id',
                'data' => $model->getServiceAttributesList(),
                'options' => ['placeholder' => 'Select attribute'],
                'id' => 'attribute-id',
                'pluginOptions' => [
                    'multiple' => false,
                    'allowClear' => true
                ]
            ]) ?>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="">Value</label>
            <?= Select2::widget([
                'name' => 'option_id',
                'id' => 'option-id',
                'data' => [],
                'options' => ['placeholder' => 'Select attribute'],
                'pluginOptions' => [
                    'multiple' => false,
                    'allowClear' => true
                ]
            ]) ?>
        </div>
    </div>
    <div class="col-md-2 text-center">
        <div class="form-group">
            <label for="">Will Trigger</label>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="">Field</label>
            <?= Select2::widget([
                'name' => 'depends_on_id',
                'id' => 'depends-on-id',
                'data' => $model->getServiceAttributesList(),
                'options' => ['placeholder' => 'Select attribute'],
                'pluginOptions' => [
                    'multiple' => false,
                    'allowClear' => true
                ]
            ]) ?>
        </div>
        <div class="form-group">
            <button type="submit" id="get-options" class="btn btn-primary">Save Rule</button>
        </div>
    </div>
</div>

<?php ActiveForm::end() ?>

<hr>

<?php foreach ($model->getServiceAttributes()->where(['deleted' => false])->all() as $serviceAttribute) { ?>

    <?php if (!$serviceAttribute->parental) {
        continue;
    } ?>

    <?php foreach ($serviceAttribute->parental as $item) { ?>
        <ul class="list-group">
            <li class="list-group-item">
                Field <b><?= $item->dependsOn->name ?></b> Value <b><?= $item->serviceAttributeOption->name ?></b> Will
                trigger
                <b><?= $item->serviceAttribute->name ?></b>
                &nbsp; &nbsp;
                <a href="<?= Url::to(['/service/remove-dependency', 'id' => $item->id ]) ?>" class="btn btn-danger btn-xs">Remove Dependency</a>
            </li>
        </ul>
    <?php } ?>

<?php } ?>


<?php

$js = <<<JS

var attributeIdSelector = '#attribute-id';
var optionsIdSelector = '#option-id';
var modelID = '{$model->id}';

console.log('selector', attributeIdSelector);

function addOptions(options){
    
    if(!options || options.length === 0){
        return false;
    }
    
    $(optionsIdSelector).val(null).trigger('change');
    
    $.each(options, function(id, name){
        var newOption = new Option(name, id, false, false);
        $(optionsIdSelector).append(newOption).trigger('change');
    });
}


function updateOptions(attribute_id){
    $.ajax({
        url: '/attribute/get-options',
        data: {attribute_id: attribute_id, service_id: modelID},
        method: 'POST',
        success: function(data){
            addOptions(data);
        }
    })
}

$(attributeIdSelector).on('select2:select', function (e) {
   var data = e.params.data;
   
   console.log(data);
   
   updateOptions(data.id);
});

JS;

$this->registerJs($js);

?>
