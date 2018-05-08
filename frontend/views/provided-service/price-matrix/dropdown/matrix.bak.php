<?php

use RummyKhan\Collection\Arr;
use yii\web\View;
use kartik\select2\Select2;

/* @var $this View */
/* @var $attributeGroups array */
/* @var $incremental array */
/* @var $matrixHeaders array */
/* @var $matrixRows array */

$columns = count($attributeGroups) > 0 ? intval(9 / count($attributeGroups)) : 0;

?>

<?php if (count($attributeGroups) > 0) { ?>

    <?php
    $class = md5('class-' . uniqid('c-', true));
    $attributes = [];
    ?>

    <?php if (!empty($incremental)) { ?>
        <div class="row">
            <div class="col-md-12">
                Price by <b><?= implode(',', $incremental) ?></b>
            </div>
        </div>
    <?php } ?>

    <div class="row">
        <?php foreach ($attributeGroups as $title => $attributeGroup) { ?>
            <?php

            $name = 'service_attribute_id_' . (Arr::first(array_keys(collect($attributeGroup)->groupBy('service_attribute_id')->toArray())));
            $attributes[$name] = $name;

            ?>
            <div class="col-md-<?= $columns ?>">
                <div class="form-group">
                    <label for=""><?= $title ?></label>
                    <?= Select2::widget([
                        'name' => $name,
                        'id' => $name,
                        'data' => collect($attributeGroup)->pluck('attribute_option_name', 'service_attribute_option_id')->toArray(),
                        'options' => ['placeholder' => 'Select ' . strtolower($title)]
                    ]) ?>
                </div>
            </div>
        <?php } ?>
        <div class="col-md-2">
            <div class="form-group">
                <label for="">Price</label>
                <input type="number" class="form-control" id="<?= $class ?>-price">
            </div>
        </div>

        <div class="col-md-1">
            <div class="form-group">
                <br>
                <button type="button" class="btn btn-primary <?= $class ?>"
                        data-prices=""
                        data-price-selector="#<?= $class ?>-price"
                        data-table-selector="#<?= $class ?>-table"
                        data-attributes="<?= implode(',', $attributes) ?>">Save
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped">
                <thead>
                <tr>
                    <?php foreach ($attributeGroups as $title => $attributeGroup) { ?>
                        <th><?= $title ?></th>
                    <?php } ?>
                    <th>Pricing</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody id="<?= $class ?>-table">

                </tbody>
            </table>
        </div>
    </div>

    <div id="<?= $class ?>-input-container">

    </div>

    <?php

    $attributeGroupJson = json_encode($attributeGroups);

    $js = <<<JS
    
    var className = '{$class}';
    
    $('body').on('click', '.'+className+'-rows', function(e){
        e.preventDefault();
        var prices = $('.'+className).attr('data-prices');
        if(!!prices){
            prices = JSON.parse(prices);
        }else{
            prices = {};
        }
        
        var id = $(this).attr('data-id');
        if(!!prices[id]){
            delete prices[id];
        }
        
        $('#'+id+'-input').remove();
        
        $('.'+className).attr('data-prices', JSON.stringify(prices));
        $('#row-'+id).remove();
        
    });
   
    $('.'+className).click(function(e){
        e.preventDefault();
        
        var attributeGroupJson = JSON.parse('{$attributeGroupJson}');
        var mergedAttributes = [];
        var attributes = $(this).attr('data-attributes').split(',');
        var priceSelector = $(this).attr('data-price-selector');
        var tableSelector = $(this).attr('data-table-selector');
        var prices = $(this).attr('data-prices');
        var inputContainerSelector = '#'+className+'-input-container';
        
        if(!!prices){
            prices = JSON.parse(prices);
        }else{
            prices = {};
        }
        
        for(var key in attributeGroupJson){
            var attributeJson = attributeGroupJson[key];
            if(!attributeJson){
                continue;
            }
            
            mergedAttributes = mergedAttributes.concat(attributeJson);
        }
        
        var findAttribute = function(values, option){
            
            for(var i=0; i<values.length; i++){
                
                var value = values[i];
                
                if(value.service_attribute_option_id === option){
                    return value;
                }
            }
            return null;
        };
        
        
        var displayPriceTable = function(values){
            $(tableSelector).empty();
            for(var id in values){
                
                var priceRow = values[id];
                var tableRow = '<tr id="row-'+id+'">';
                
                $.each(priceRow.matrix, function(index, matrixRow){
                    tableRow += '<td>'+matrixRow.attribute_option_name+'</td>';
                });
                
                tableRow += '<td>'+priceRow.price+'</td>';
                tableRow += '<td><a href="#" data-id="'+id+'" class="btn btn-danger btn-xs '+className+'-rows"><i class="glyphicon glyphicon-trash"></i></a></td>';
                tableRow += '</tr>';
                
                $(tableSelector).append(tableRow);
                $(tableSelector).append('<input type="hidden" name="matrix_price['+id+']" value="'+priceRow.price+'" id="'+id+'-input">')
            }
        };
        
        var matrixRow = [];
        var keys = [];
        for(var i=0; i< attributes.length; i++){
            var attribute = attributes[i];
            
            var selector = '#'+attribute;
            
            if(!$(selector).val()){
                return false;
            }
            
            var attributeWithOption = findAttribute(mergedAttributes, $(selector).val());
            
            if(!attributeWithOption){
                return false;
            }
            
            matrixRow.push(attributeWithOption);
            keys.push($(selector).val()); 
        }
        
        var price = parseFloat($(priceSelector).val());
        
        if(isNaN(price)){
            return false;
        }
        
        prices[keys.join('_')] = {matrix: matrixRow, price: price};
        $(this).attr('data-prices', JSON.stringify(prices));
        displayPriceTable(prices);
    });

JS;

    $this->registerJs($js);

    ?>

<?php } ?>