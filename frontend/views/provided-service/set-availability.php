<?php

use common\helpers\AvailabilityHelper;
use common\models\RuleValueType;
use common\models\RequestType;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;
use kartik\select2\Select2;
use frontend\assets\AvailabilityAsset;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $model common\models\ProvidedService */
/* @var $provider common\models\Provider */
/* @var $service common\models\Service */
/* @var $type string */
/* @var $matrixHeaders array */
/* @var $matrixRows array */
/* @var $noImpactRows array */
/** @var $area \common\models\ProvidedServiceArea */
/** @var $providedServiceType \common\models\ProvidedRequestType */
/** @var $globalRules array */
/** @var $localRules array */

AvailabilityAsset::register($this);

$this->title = 'Add Availability for ' . $area->name;
$this->params['breadcrumbs'][] = ['label' => $provider->username, 'url' => ['/provider/view', 'id' => $model->provider_id]];
$this->params['breadcrumbs'][] = ['label' => 'Provided Services', 'url' => ['/provided-service/index', 'provider_id' => $model->provider_id]];
$this->params['breadcrumbs'][] = ['label' => $service->name, 'url' => ['/provided-service/view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = ['label' => $providedServiceType->serviceType->type];
$this->params['breadcrumbs'][] = ['label' => 'Coverage Areas', 'url' => ['/provided-service/view-coverage-areas', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;

?>

    <div class="row">
        <div class="col-md-12">

            <?php Modal::begin([
                'header' => 'Add Availability Rules',
                'toggleButton' => ['label' => 'Add Availability Rules', 'class' => 'btn btn-primary'],
                'size' => Modal::SIZE_LARGE
            ]) ?>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">Type</label>
                        <?= Select2::widget([
                            'name' => 'arType',
                            'data' => AvailabilityHelper::toList(),
                            'id' => 'ar-type',
                            'options' => ['placeholder' => 'Select Type'],
                            'pluginOptions' => [
                                'multi' => false,
                                'allowClear' => true,
                            ]
                        ]) ?>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Day</label>
                        <?= Select2::widget([
                            'name' => 'day',
                            'data' => [
                                'All' => 'All',
                                'Fri' => 'Fri',
                                'Sat' => 'Sat',
                                'Sun' => 'Sun',
                                'Mon' => 'Mon',
                                'Tue' => 'Tue',
                                'Wed' => 'Wed',
                                'Thu' => 'Thu',
                            ],
                            'id' => 'ar-day',
                            'options' => ['placeholder' => 'Select Day'],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'multi' => false
                            ]
                        ]) ?>
                    </div>
                </div>

                <div class="col-md-3" id="ar-start-hour-container">
                    <div class="form-group">
                        <label for="">Start Hour</label>
                        <?= Select2::widget([
                            'name' => 'start_time',
                            'id' => 'ar-start-time',
                            'data' => range(0, 23),
                            'options' => ['placeholder' => 'Select Start Time'],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'multi' => false
                            ]
                        ]) ?>
                    </div>
                </div>

                <div class="col-md-3" id="ar-end-hour-container">
                    <div class="form-group">
                        <label for="">End Hour</label>
                        <?= Select2::widget([
                            'name' => 'end_time',
                            'data' => range(0, 23),
                            'id' => 'ar-end-time',
                            'options' => ['placeholder' => 'Select End Time'],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'multi' => false
                            ]
                        ]) ?>
                    </div>
                </div>
            </div>

            <div class="row hidden" id="global-price-value-container">

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="">Increase / Decrease</label>
                        <?= Select2::widget([
                            'name' => 'global-rule-price-type',
                            'data' => [
                                'increase' => 'Increase',
                                'decrease' => 'Decrease',
                            ],
                            'id' => 'date-rule-price-type',
                            'options' => ['placeholder' => 'Select price type'],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'multi' => false
                            ]
                        ]) ?>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="">Percentage / Fixed</label>
                        <?= Select2::widget([
                            'name' => 'global-rule-update-as',
                            'data' => RuleValueType::toList(),
                            'id' => 'date-rule-update-as',
                            'options' => ['placeholder' => 'Select price type'],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'multi' => false
                            ]
                        ]) ?>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="">Updated Price</label>
                        <input type="text" id="global-rule-updated-price" class="form-control">
                    </div>
                </div>

            </div>

            <div class="row">

                <div class="col-md-3">
                    <div class="form-group">
                        <br>
                        <button type="button" class="btn btn-primary" id="add-ar-rule">Add Rule</button>
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="col-md-12">

                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Type</th>
                            <th>Day</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Increase/Decrease</th>
                            <th>Percentage/Fixed</th>
                            <th>Value</th>
                        </tr>
                        </thead>
                        <tbody id="ar-rules-container">

                        </tbody>
                    </table>


                </div>
            </div>

            <div class="row hidden" id="update-rules-row">
                <div class="col-md-12 text-right">
                    <?php ActiveForm::begin(['action' => ['/provided-service/add-global-rules', 'id' => $model->id, 'area' => $area->id, 'type' => $type]]) ?>
                    <input type="hidden" name="global-rules" value="" id="availability-rules">
                    <button class="btn btn-primary">Apply Rules</button>
                    <?php ActiveForm::end() ?>
                </div>
            </div>

            <?php Modal::end() ?>

        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div id="year-calendar"></div>
        </div>
    </div>


<?php Modal::begin([
    'header' => 'Add rules for <span id="date-rule-modal-date">Add Availability Rules for the day</span>',
    'size' => Modal::SIZE_LARGE,
    'id' => 'date-modal'
]) ?>


    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="">Type</label>
                <?= Select2::widget([
                    'name' => 'date-rule-type',
                    'data' => AvailabilityHelper::toList(),
                    'id' => 'date-rule-type',
                    'options' => ['placeholder' => 'Select rule type'],
                    'pluginOptions' => [
                        'multi' => false,
                        'allowClear' => true,
                    ]
                ]) ?>
            </div>
        </div>
    </div>

    <div class="row">

        <div class="col-md-6">
            <div class="form-group">
                <label for="">Start Time</label>
                <?= Select2::widget([
                    'name' => 'date-rule-start-time',
                    'id' => 'date-rule-start-time',
                    'data' => range(0, 23),
                    'options' => ['placeholder' => 'Select Start Time'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'multi' => false
                    ]
                ]) ?>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="">End Time</label>
                <?= Select2::widget([
                    'name' => 'date-rule-end-time',
                    'data' => range(0, 23),
                    'id' => 'date-rule-end-time',
                    'options' => ['placeholder' => 'Select End Time'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'multi' => false
                    ]
                ]) ?>
            </div>
        </div>
    </div>

    <div class="row hidden" id="date-price-value-container">

        <div class="col-md-4">
            <div class="form-group">
                <label for="">Increase / Decrease</label>
                <?= Select2::widget([
                    'name' => 'global-rule-price-type',
                    'data' => [
                        'increase' => 'Increase',
                        'decrease' => 'Decrease',
                    ],
                    'id' => 'global-rule-price-type',
                    'options' => ['placeholder' => 'Select price type'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'multi' => false
                    ]
                ]) ?>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label for="">Percentage / Fixed</label>
                <?= Select2::widget([
                    'name' => 'global-rule-update-as',
                    'data' => RuleValueType::toList(),
                    'id' => 'global-rule-update-as',
                    'options' => ['placeholder' => 'Select price type'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'multi' => false
                    ]
                ]) ?>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label for="">Updated Price</label>
                <input type="text" id="date-rule-updated-price" class="form-control">
            </div>
        </div>

    </div>

    <div class="row">

        <div class="col-md-3">
            <div class="form-group">
                <br>
                <button type="button" class="btn btn-primary" id="add-date-rule">Add Rule</button>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-md-12">

            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Type</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Increase/Decrease</th>
                    <th>Percentage/Fixed</th>
                    <th>Value</th>
                </tr>
                </thead>
                <tbody id="date-rules-table">

                </tbody>
            </table>


        </div>
    </div>

    <hr>

    <div class="row">
        <div class="col-md-11">
            <h4>Availability for this date</h4>
            <nav>
                <ul class="pagination" id="date-availability-hours">

                </ul>
            </nav>
        </div>
    </div>

    <!--
    <hr>

    <div class="row">
        <div class="col-md-12">
            <h4>Applied Rules</h4>
            <table class="table table-striped table-responsive">
                <thead>
                    <tr>
                        <th>Rule Type</th>
                        <th>Day/Date</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Available</th>
                        <th>Increase/Decrease</th>
                        <th>Percentage/Fixed</th>
                        <th>Value</th>
                    </tr>
                </thead>
                <tbody id="date-applied-rules">

                </tbody>
            </table>
        </div>
    </div>

    -->


    <div class="row hidden" id="update-date-rule">
        <div class="col-md-12">

            <?php ActiveForm::begin(['action' => ['/provided-service/add-date-rule', 'id' => $model->id, 'area' => $area->id, 'type' => $type]]) ?>
            <input type="hidden" id="date-rules" name="date-rules" value="" required>
            <input type="hidden" id="date-rules-date" name="date" value="" required>
            <button class="btn btn-primary">Submit</button>
            <?php ActiveForm::end() ?>

        </div>
    </div>


<?php Modal::end() ?>

<?php

$startDate = date('Y-m-d');
$endDate = date('Y-12-31', strtotime('+1 years', strtotime($startDate)));
$endDate = date('Y-12-31');
$globalRulesJson = json_encode($globalRules);
$localRulesJson = json_encode($localRules);

$js = <<<JS

var startDate = '{$startDate}';
var endDate = '{$endDate}';

var startDateMoment = moment(startDate, 'YYYY-MM-DD');
var endDateMoment = moment(endDate, 'YYYY-MM-DD');

// Adding Availability Rules
var ARTypeSelector = '#ar-type';
var ARDaySelector = '#ar-day';
var ARStartTimeSelector = '#ar-start-time';
var AREndTimeSelector = '#ar-end-time';
var ARGlobalPriceValueContainer = '#global-price-value-container';
var ARRulePriceTypeSelector = '#global-rule-price-type';
var ARRuleUpdateAsSelector = '#global-rule-update-as';
var ARRuleUpdatePriceSelector = '#global-rule-updated-price';

var availabilityRules = JSON.parse('{$globalRulesJson}');

// Adding Rule by Date
var dateRuleTypeSelector = '#date-rule-type';
var datePriceValueContainerSelector = '#date-price-value-container';
var dateRulesTableSelector = '#date-rules-table';

var dateRuleStartTimeSelector = '#date-rule-start-time';
var dateRuleEndTimeSelector = '#date-rule-end-time';
var dateRulePriceTypeSelector = '#date-rule-price-type';
var dateRuleUpdateAsSelector = '#date-rule-update-as';
var dateRuleUpdatePriceSelector = '#date-rule-updated-price';
var dateRuleModalDateSelector = '#date-rule-modal-date';
var dateRuleUpdateSelector = '#update-date-rule';
var dateRulesSelector = '#date-rules';
var dateRulesDateSelector = '#date-rules-date';
var dateRuleAvailabilityHoursSelector = '#date-availability-hours';
var dateRuleAppliedRules = '#date-applied-rules';

var dateAvailabilityRules = JSON.parse('{$localRulesJson}');


// Entry Scripts
var manager = new Availability(availabilityRules, dateAvailabilityRules);
refreshRulesDisplay(availabilityRules);
initializeCalendar();

function dayClicked(date, rules){
    var momentDate = moment(date);
    
    $('#date-modal').modal();
    $(dateRuleModalDateSelector).text(momentDate.format('YYYY-MM-DD'));
    $(dateRulesDateSelector).val(momentDate.format('YYYY-MM-DD'));
    refreshDateRuleTableDisplay(dateAvailabilityRules);
    showDateAvailability(rules);
    //showDateAppliedRules(rules);
}

function addAppliedRuleRow(rule, type){
    var tableRow = '<tr>'+
        '<td>'+type+'</td>'+
         '<td>'+(!!rule.day ? rule.day : (!!rule.date ? rule.date : '') )+'</td>'+
         '<td>'+rule.start_time+'</td>'+
         '<td>'+rule.end_time+'</td>'+
         '<td>'+(rule.type)+'</td>'+
         (!!rule.price_type ? '<td>'+rule.price_type+'</td>' : '<td></td>')+
         (!!rule.update_as ? '<td>'+rule.update_as+'</td>' : '<td></td>')+
         (!!rule.value ? '<td>'+rule.value+'</td>' : '<td></td>')+
         '</tr>';
    
    $(dateRuleAppliedRules).append(tableRow);
}

function showDateAppliedRules(rules){
    
    $(dateRuleAppliedRules).empty();
    
    rules = JSON.parse(rules);
    
    $.each(rules.globalAvailability, function(i, value){
        addAppliedRuleRow(value, 'Day Rule');
    });
    
    $.each(rules.globalException, function(i, value){
        addAppliedRuleRow(value, 'Day Rule');
    });
    
    $.each(rules.localAvailability, function(i, value){
        addAppliedRuleRow(value, 'Date Rule');
    });
    
    $.each(rules.locationExceptions, function(i, value){
        addAppliedRuleRow(value, 'Date Rule');
    });
}

function showDateAvailability(rules){
    $(dateRuleAvailabilityHoursSelector).empty();
    rules = JSON.parse(rules);
    
    for(var i=0; i<=23; i++){
        
        var elementClass = '';
        
        if(rules.merged.indexOf(i) > -1){
            elementClass = 'active';
        }else{
            elementClass = 'disabled';
        }
        
        var elementHtml = '<li class="'+elementClass+'"><a href="#">'+i+' <span class="sr-only">'+i+'</span></a></li>';
        
        $(dateRuleAvailabilityHoursSelector).append(elementHtml);
    }
}

function initializeCalendar(){
    $('#year-calendar').calendar({
        startYear: moment().format('Y'),
        minDate: startDateMoment.toDate(),
        maxDate: endDateMoment.toDate(),
        startMonth: startDateMoment.format('MM'),
        clickDay: function(e){
            
            var rules = $(e.element).find('div.day-content').attr('data-rules');
            
            dayClicked(e.date, rules);
            
        },
        customDayRenderer: function(element, date){
            var momentDate = moment(date);
            
            if(momentDate.isBefore(startDateMoment)){
                return true;
            }
            
            var data = manager.getAvailabilityData(momentDate);
            
            // mean we have availability after all the merges..
            if(data.merged.length > 0){
                
                var opacity = (data.merged.length/9).toFixed(1);
                
                $(element).parent().css('box-shadow', 'rgb(156, 183, 3) 0px -4px 0px 0px inset');
                $(element).attr('data-rules', JSON.stringify(data));
                
            }
        }
    });
}

// Adding Availability Rules
function refreshRulesDisplay(rules){
    var arBody = $('#ar-rules-container');
    arBody.empty();
    
    $.each(rules, function(index, value){
        var tableRow = 
        '<tr>'+
            '<td>'+value.type+'</td>'+
            '<td>'+value.day+'</td>'+
            '<td>'+value.start_time+'</td>'+
            '<td>'+value.end_time+'</td>'+
            (!!value.price_type ? '<td>'+value.price_type+'</td>' : '<td></td>')+
            (!!value.update_as ? '<td>'+value.update_as+'</td>' : '<td></td>')+
            (!!value.value ? '<td>'+value.value+'</td>' : '<td></td>')+
        '</tr>';
        arBody.append(tableRow);
    });
    
    if(rules.length > 0){
        var updateRulesActionContainer = $('#update-rules-row');
        
        if($(updateRulesActionContainer).hasClass('hidden')){
            $(updateRulesActionContainer).removeClass('hidden');
        }
    }
}

function isAlreadyAdded(identifier, rules){
    for(var i=0; i<rules.length; i++){
        var rule = rules[i];
        if(!rule){
            continue;
        }
        
        if(rule['identifier'] === identifier){
            return true;
        }
    }
    return false;
}


$('#add-ar-rule').click(function(e){
    var type = $(ARTypeSelector).val();
    
    if(!type){
        return false;
    }
    
    var day = $(ARDaySelector).val();
    var startTime = $(ARStartTimeSelector).val();
    var endTime = $(AREndTimeSelector).val();
    
    if(!!day && !!startTime && !!endTime){
        
        var identifier = day+startTime+endTime+type;
        
        if(!isAlreadyAdded(identifier, availabilityRules)){
            availabilityRules.push({
                day: day,
                start_time: startTime,
                end_time: endTime,
                type: type,
                identifier: identifier,
                price_type: $(ARRulePriceTypeSelector).val(),
                update_as: $(ARRuleUpdateAsSelector).val(),
                value: $(ARRuleUpdatePriceSelector).val(),
            });
            refreshRulesDisplay(availabilityRules);
        }
    }
   
    $(ARDaySelector).val('').trigger('change');
    $(ARStartTimeSelector).val('').trigger('change');
    $(AREndTimeSelector).val('').trigger('change');
    $(ARTypeSelector).val('').trigger('change');
    $(ARRulePriceTypeSelector).val('').trigger('change');
    $(ARRuleUpdateAsSelector).val('').trigger('change');
    $(ARRuleUpdatePriceSelector).val('');
    
    $('#availability-rules').val(JSON.stringify(availabilityRules));
});

$(ARTypeSelector).on('select2:select', function(e){
    
    var data = e.params.data;
    
    if(data.id === 'Available'){
        $(ARGlobalPriceValueContainer).removeClass('hidden');
    }else{
        if(!$(ARGlobalPriceValueContainer).hasClass('hidden')){
            $(ARGlobalPriceValueContainer).addClass('hidden');
        }
    }
});

$(dateRuleTypeSelector).on('select2:select', function(e){
    
    var data = e.params.data;
    
    if(data.id === 'Available'){
        $(datePriceValueContainerSelector).removeClass('hidden');
    }else{
        if(!$(datePriceValueContainerSelector).hasClass('hidden')){
            $(datePriceValueContainerSelector).addClass('hidden');
        }
    }
});

function refreshDateRuleTableDisplay(rules) {
  $(dateRulesTableSelector).empty();
  
  rules = _.filter(rules, {date: $(dateRuleModalDateSelector).text()}) || [];
  
  
  $.each(rules, function(index, value){
      
      var tableRow = '<tr>'+
         '<td>'+value.type+'</td>'+
         '<td>'+value.start_time+'</td>'+
         '<td>'+value.end_time+'</td>'+
         (!!value.price_type ? '<td>'+value.price_type+'</td>' : '<td></td>')+
         (!!value.update_as ? '<td>'+value.update_as+'</td>' : '<td></td>')+
         (!!value.value ? '<td>'+value.value+'</td>' : '<td></td>')+
         '</tr>';
      
     $(dateRulesTableSelector).append(tableRow);
  });
  
  if(rules.length > 0){
      if($(dateRuleUpdateSelector).hasClass('hidden')){
          $(dateRuleUpdateSelector).removeClass('hidden');
      }
  }else{
      if(!$(dateRuleUpdateSelector).hasClass('hidden')){
          $(dateRuleUpdateSelector).addClass('hidden');
      }
  }
}

function refreshDateControls(){
    $(dateRuleStartTimeSelector).val('').trigger('change');
    $(dateRuleEndTimeSelector).val('').trigger('change');
    $(dateRulePriceTypeSelector).val('').trigger('change');
    $(dateRuleUpdateAsSelector).val('').trigger('change');
    $(dateRuleUpdatePriceSelector).val('').trigger('change');
    $(dateRuleTypeSelector).val('').trigger('change');
}

function addDateAvailableRule( ){
    var startTime = $(dateRuleStartTimeSelector).val();
    var endTime = $(dateRuleEndTimeSelector).val();
    var priceType = $(dateRulePriceTypeSelector).val();
    var updateAs = $(dateRuleUpdateAsSelector).val();
    var value = $(dateRuleUpdatePriceSelector).val();
    
    if(!!startTime && !!endTime){
        var identifier = startTime+endTime+'Available'+$(dateRuleModalDateSelector).text();
        var obj = {
            start_time: startTime,
            end_time: endTime,
            type: 'Available',
            price_type: null,
            update_as: null,
            value: null,
            date: $(dateRuleModalDateSelector).text(),
            identifier: identifier
        };
        
        if(!!priceType && !!updateAs && !!value){
            obj['price_type'] = priceType;
            obj['update_as']  = updateAs;
            obj['value'] = value;
        }
        
        if(!isAlreadyAdded(identifier, dateAvailabilityRules)){
            dateAvailabilityRules.push(obj);
        }
    }
    
    refreshDateRuleTableDisplay(dateAvailabilityRules);
    refreshDateControls();
}

function addDateNotAvailableRule( ){
    var startTime = $(dateRuleStartTimeSelector).val();
    var endTime = $(dateRuleEndTimeSelector).val();
    
    if(!!startTime && !!endTime){
        
        var identifier = startTime+endTime+'Not Available'+$(dateRuleModalDateSelector).text();
        
        var obj = {
            start_time: startTime,
            end_time: endTime,
            type: 'Not Available',
            price_type: null,
            update_as: null,
            value: null,
            date: $(dateRuleModalDateSelector).text(),
            identifier: identifier
        };
        
        if(!isAlreadyAdded(identifier, dateAvailabilityRules)){
            dateAvailabilityRules.push(obj);
        }
    }
    
    refreshDateRuleTableDisplay(dateAvailabilityRules);
    refreshDateControls();
}


$('#add-date-rule').click(function(e){
    var type = $(dateRuleTypeSelector).val();
    
    if(!type){
        return false;
    }
    
    if(type === 'Available'){
        addDateAvailableRule();
    }else{
        addDateNotAvailableRule();
    }
    
    if(dateAvailabilityRules.length > 0){
        $(dateRulesSelector).val(JSON.stringify(dateAvailabilityRules));
    }
});

JS;


$this->registerJs($js);

?>