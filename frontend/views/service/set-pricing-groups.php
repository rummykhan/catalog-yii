<?php

use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\DetailView;
use frontend\assets\JQueryUiAsset;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $service common\models\Service */
/* @var $model \common\forms\AttachAttribute */

$this->title = 'Set pricing groups';
$this->params['breadcrumbs'][] = ['label' => 'Services', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $service->name, 'url' => ['/service/view', 'id' => $service->id]];
$this->params['breadcrumbs'][] = $this->title;

JQueryUiAsset::register($this);
?>

<?php

$css = <<<CSS
#sortable1, #sortable2, #sortable3 { background: #eee; padding: 5px;}
CSS;

$this->registerCss($css);

?>

<?php if (count($service->getServiceAttributesListNotInPriceGroup()) > 0) { ?>

    <div class="row">

        <div class="col-md-3">
            <h4>Fields</h4>
            <hr>
            <ul class="list-group droptrue" id="sortable1">
                <?php foreach ($service->getServiceAttributesListNotInPriceGroup() as $attribute) { ?>
                    <li class="list-group-item" data-id="<?= $attribute->id ?>"><?= $attribute->name ?></li>
                <?php } ?>
            </ul>
        </div>

        <div class="col-md-3">
            <h4>Field Group</h4>
            <hr>
            <ul class="list-group droptrue" id="sortable2">

            </ul>
        </div>

        <div class="col-md-3 hidden" id="group-info">

            <h4>Add Group Info</h4>
            <hr>

            <?php $form = ActiveForm::begin() ?>

            <?= $form->field($model, 'group_name')->textInput() ?>

            <?php if ($service->getPricingAttributeGroups()->count() > 0) { ?>
                <h4>OR</h4>

                <?= Select2::widget([
                    'model' => $model,
                    'attribute' => 'group_id',
                    'data' => collect($service->getPricingAttributeGroups()->asArray()->all())->pluck('name', 'id')->toArray(),
                    'options' => ['placeholder' => 'Select existing group'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'multiple' => false,
                    ]
                ]) ?>
            <?php } ?>

            <?= $form->field($model, 'service_attributes', ['template' => '{input}'])->hiddenInput() ?>

            <div class="form-group">
                <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
            </div>


            <?php ActiveForm::end() ?>

        </div>

    </div>

    <hr>

<?php } ?>

<div class="row">
    <?php foreach ($service->pricingAttributeGroups as $pricingAttributeGroup) { ?>
        <?php if (count($pricingAttributeGroup->pricingAttributes) == 0) {
            $pricingAttributeGroup->delete();
            continue;
        } ?>
        <div class="col-md-4">
            <h4>Group: <?= $pricingAttributeGroup->name ?></h4>
            <ul class="list-group">

                <?php foreach ($pricingAttributeGroup->pricingAttributes as $pricingAttribute) { ?>
                    <li class="list-group-item">
                        <?= $pricingAttribute->serviceAttribute->name ?>
                        <a href="#"
                           onclick="if(confirm('Are you sure?')){window.location.href = $(this).attr('data-href');}"
                           data-href="<?= \yii\helpers\Url::to([
                               '/service/remove-pricing-attribute',
                               'id' => $service->id,
                               'group_id' => $pricingAttributeGroup->id,
                               'service_attribute_id' => $pricingAttribute->id
                           ]) ?>" class="btn btn-danger btn-xs pull-right"><i class="glyphicon glyphicon-trash"></i></a>
                    </li>
                <?php } ?>

            </ul>
        </div>
    <?php } ?>
</div>


<?php

$attributesSelector = Html::getInputId($model, 'service_attributes');

$js = <<<JS


var groupInfoSelector = '#group-info';
var groupAttributesSelector = '#{$attributesSelector}';

function hideGroupInfo(){
    if(!$(groupInfoSelector).hasClass('hidden')){
        $(groupInfoSelector).addClass('hidden');
    }
}

function showGroupInfo(){
 $(groupInfoSelector).removeClass('hidden');   
}

function removeGroupIds(target){
    $(groupAttributesSelector).val('');
}

function addGroupIds(target){
    var attributes = [];
    
    $.each(target.find('li'), function(i, v){
        attributes.push($(v).attr('data-id'));
    });
    
    $(groupAttributesSelector).val(JSON.stringify(attributes));
}

$( "#sortable1" ).sortable({
  connectWith: "ul",
  dropOnEmpty: true,
});

$( "#sortable2" ).sortable({
  connectWith: "ul",
  receive: function( event, ui ) {
     var target = $('#sortable2');
      
      if(target.find('li').length === 0){
          hideGroupInfo();
          removeGroupIds(target)
      }else{
          showGroupInfo();
          addGroupIds(target);
      }
      
      
  }
});
JS;


$this->registerJs($js);

?>
