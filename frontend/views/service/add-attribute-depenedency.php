<?php

use common\models\ServiceCompositeAttribute;
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
                'name' => 'parent',
                'data' => $model->getServiceAttributesList(),
                'id' => 'attribute-id',
                'options' => ['placeholder' => 'Select attribute'],
                'pluginOptions' => [
                    'multiple' => false,
                    'allowClear' => true
                ]
            ]) ?>
        </div>
        <ul class="list-group" id="option-id" data-name="parent-options">
        </ul>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="">Will Trigger</label>
            <?= Select2::widget([
                'name' => 'child',
                'id' => 'depends-on-id',
                'data' => [],
                'pluginOptions' => [
                    'multiple' => false,
                    'allowClear' => true
                ]
            ]) ?>
        </div>
        <ul class="list-group" id="depends-on-option-id" data-name="child-options">
        </ul>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <input type="submit" value="Submit" class="btn btn-primary">
    </div>
</div>

<?php ActiveForm::end() ?>

<?php foreach (array_chunk($model->getDependencyTable(), 3) as $rows) { ?>
    <div class="row">
        <?php foreach ($rows as $row) { ?>

            <?php if (count(ServiceCompositeAttribute::getOptions($row['id'])) === 0) {
                continue;
            } ?>

            <div class="col-md-4">

                <h4><?= $row['name'] ?></h4>

                <ul class="list-group">
                    <?php foreach (ServiceCompositeAttribute::getOptions($row['id']) as $option) { ?>


                        <li class="list-group-item">
                            <a href="#" class="expand-child"
                               data-attribute-name="<?= $row['name'] ?>"
                               data-option-name="<?= $option['option_name'] ?>"
                               data-service-id="<?= $model->id ?>"
                               data-attribute-id="<?= $row['id'] ?>"
                               data-option-id="<?= $option['option_id'] ?>">

                                <?= $option['option_name'] ?>

                            </a>

                            <a href="#" data-href="<?= Url::to(['/service/remove-attribute-dependency', 'id' => $model->id, 'attribute_id' => $row['id'], 'option_id' => $option['option_id']]) ?>"
                               onclick="if(confirm('Are you sure')){window.location.href = $(this).attr('data-href');}"
                               class="btn btn-danger btn-xs pull-right"><i class="glyphicon glyphicon-trash"></i></a>

                        </li>
                    <?php } ?>
                </ul>
            </div>
        <?php } ?>
    </div>
<?php } ?>

<div class="modal fade" id="dependency-view" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <div class="modal-title">Modal title</div>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<?php

$attributes = json_encode($model->getServiceAttributesList());

$js = <<<JS

var attributeIdSelector = '#attribute-id';
var optionsIdSelector = '#option-id';
var dependsOnIdSelector = '#depends-on-id';
var dependsOnOptionsIdSelector = '#depends-on-option-id';
var modelID = '{$model->id}';
var attributes = JSON.parse('$attributes');

function addOptions(options, element){
    
    if(!options || options.length === 0){
        return false;
    }
    
    $.each(options, function(id, name){
        
        var inputName = element.attr('data-name');
        
        var input = '<input type="checkbox" name="'+inputName+'['+id+']">';
        
        var inputGroup = '<div class="input-group">'+
                            '<span class="input-group-addon">'+
                                input +
                            '</span>'+
                            '<input type="text" class="form-control" value="'+name+'" disabled="disabled">'+
                         '</div>';
        
        element.append('<li class="list-group-item">'+inputGroup+'</li>');
    });
}

function populateDropDown(fieldsList, selectedID, elementSelector){
    
    // clear next
    $(elementSelector).html(null).trigger('change');
    
    
    var option = new Option('Select attribute', 0, false, false);
    $(elementSelector).append(option).trigger('change');
    
    
    $.each(fieldsList, function(id, name){
        
        if(id !== selectedID){
            var option = new Option(name, id, false, false);
            $(elementSelector).append(option).trigger('change');
        } 
    });
}


function updateOptions(attribute_id, element){
    $.ajax({
        url: '/attribute/get-options',
        data: {attribute_id: attribute_id, service_id: modelID},
        method: 'POST',
        success: function(data){
            addOptions(data, element);
        }
    })
}

function fetchOptions(id, element, inputType){
   $(element).empty();
   
   updateOptions(id, element, inputType);
}

$(attributeIdSelector).on('select2:select', function (e) {
    var data = e.params.data;
    fetchOptions(data.id, $(optionsIdSelector));
    $(dependsOnOptionsIdSelector).empty();
    populateDropDown(attributes, data.id, dependsOnIdSelector);
    
});

$(dependsOnIdSelector).on('select2:select', function (e) {
    var data = e.params.data;
    fetchOptions(data.id, $(dependsOnOptionsIdSelector));
});

function appendChilds(element, childs, attribute_id, option_id, attribute_name, option_name){
    
    if (childs.length === 0){
        return true;
    }
    
    var attributeTitle = "";
    
    var list = '<ul class="list-group">';
    $.each(childs, function(index, child){
        attributeTitle = child.attribute_name;
        list += '<li class="list-group-item" style="padding-left:20px;">'+child.option_name+'</li>';
    });
    list += '</ul>';
    
    var title = "Attribute: <b>"+attribute_name + "</b>, Option <b>" + option_name + "</b> Will trigger";
    
    list = '<h4> Attribute: '+attributeTitle+'</h4>'  + list;
    
    
    $('#dependency-view').find('.modal-body').empty();
    $('#dependency-view').modal();
    $('#dependency-view').find('.modal-body').append(list);
    $('#dependency-view').find('.modal-title').empty();
    $('#dependency-view').find('.modal-title').append(title);
    
}


function fetchChilds(element, service_id, attribute_id, option_id, attribute_name, option_name){
    $.ajax({
        url: '/service/get-childs',
        data: {
            service_id: service_id,
            attribute_id: attribute_id,
            option_id: option_id,
        },
        method: 'POST',
        success: function(data){
            console.log('data', data);
            appendChilds(element, data, attribute_id, option_id, attribute_name, option_name);
        }
    })
}

$('.expand-child').click(function(e){
    e.preventDefault();
    
    var element = $(this);
    fetchChilds(
        element,
        element.attr('data-service-id'),
        element.attr('data-attribute-id'), 
        element.attr('data-option-id'),
        element.attr('data-attribute-name'),
        element.attr('data-option-name')
    );
})

JS;

$this->registerJs($js);

?>
