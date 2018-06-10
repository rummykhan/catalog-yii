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
/* @var $model \common\forms\AddCalendar */
/* @var $provider common\models\Provider */
/** @var $globalRules array */
/** @var $localRules array */

AvailabilityAsset::register($this);

$this->title = "Add Calendar";
$this->params['breadcrumbs'][] = ['label' => 'Providers', 'url' => ['/provider/index']];
$this->params['breadcrumbs'][] = ['label' => $provider->username, 'url' => ['/provider/view', 'id' => $provider->id]];
$this->params['breadcrumbs'][] = ['label' => 'Calendars', 'url' => ['/provider/availability', 'provider_id' => $provider->id]];
$this->params['breadcrumbs'][] = ['label' => $this->title];

?>

<?php

$css = <<<CSS
    .availability > li > a, .availability > li > span {
        position: relative;
        float: left;
        padding: 5px 10px;
        margin-left: -1px;
        line-height: 1.42857143;
        color: #337ab7;
        text-decoration: none;
        background-color: #fff;
        border: 1px solid #ddd;
    }
CSS;

$this->registerCss($css);

?>


    <div class="row">
        <div class="col-md-8">
            <div id="year-calendar"></div>
        </div>
        <div class="col-md-4">

            <?php $form = ActiveForm::begin() ?>

            <?= $form->field($model, 'name')->textInput() ?>

            <?= $form->field($model, 'globalRules', ['template' => "{input}"])->hiddenInput() ?>
            <?= $form->field($model, 'dateRules', ['template' => "{input}"])->hiddenInput() ?>

            <div class="form-group">
                <button class="btn btn-primary"><?= !empty($model->calendarId) ? 'Update' : 'Save' ?></button>
                <button type="button" class="btn btn-primary" id="add-availability-rule">Add Availability Rule</button>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <h4 class="hidden" id="global-rules-list-title">Global Rules</h4>
                    <ul class="list-group" id="global-rules-list">
                    </ul>
                    <h4 class="hidden" id="date-rules-list-title">Date Rules</h4>
                    <ul class="list-group" id="date-rules-list">
                    </ul>
                </div>
            </div>

            <?php ActiveForm::end() ?>

        </div>
    </div>

<?php Modal::begin([
    'header' => 'Add Availability Rules',
    'size' => Modal::SIZE_LARGE,
    'id' => 'global-modal'
]) ?>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="">Type</label>
                <?= Select2::widget([
                    'name' => 'arType',
                    'data' => AvailabilityHelper::toList(),
                    'id' => 'global-availability-type',
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
                    'id' => 'global-day',
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
                    'id' => 'global-start-time',
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
                    'id' => 'global-end-time',
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
                    <th></th>
                    <th>Type</th>
                    <th>Day</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Increase/Decrease</th>
                    <th>Percentage/Fixed</th>
                    <th>Value</th>
                </tr>
                </thead>
                <tbody id="global-rules-container">

                </tbody>
            </table>


        </div>
    </div>

<?php Modal::end() ?>

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
        <div class="col-md-12 text-center">
            <h4>Availability</h4>
            <ul class="pagination availability" id="date-availability-hours">
            </ul>
        </div>
    </div>

<?php Modal::end() ?>

<?php

//TODO: Fix the below line to support the udpate
$globalRules = json_decode($model->globalRules, true);
$localRules = json_decode($model->dateRules, true);

$startDate = date('Y-m-d');
$endDate = date('Y-12-31');

$globalRulesJson = json_encode($globalRules);
$localRulesJson = json_encode($localRules);

$globalRulesSelector = Html::getInputId($model, 'globalRules');
$dateRulesSelector = Html::getInputId($model, 'dateRules');

$js = <<<JS
    
    $('#year-calendar').providerCalendar({
    
       startDate: '{$startDate}',
       endDate: '{$endDate}',

       GlobalAvailabilityTypeSelector: '#global-availability-type',
       GlobalDaySelector: '#global-day',
       GlobalStartTimeSelector: '#global-start-time',
       GlobalEndTimeSelector: '#global-end-time',
       GlobalPriceValueContainer: '#global-price-value-container',
       GlobalRulePriceTypeSelector: '#global-rule-price-type',
       GlobalRuleUpdateAsSelector: '#global-rule-update-as',
       GlobalRuleUpdatePriceSelector: '#global-rule-updated-price',
       GlobalRulesContainer: '#global-rules-container',
       GlobalModalTriggerSelector: '#add-availability-rule',
       GlobalModal: '#global-modal',
       GlobalRulesListTitle: '#global-rules-list-title',
       GlobalRulesList: '#global-rules-list',
       GlobalAvailabilityRules: JSON.parse('{$globalRulesJson}'),
       GlobalRulesInputSelector: '#{$globalRulesSelector}',
       /*   =======================================================  */
       
       DateRuleTypeSelector: '#date-rule-type',
       DatePriceValueContainerSelector: '#date-price-value-container',
       DateRulesTableSelector: '#date-rules-table',
       DateRuleStartTimeSelector: '#date-rule-start-time',
       DateRuleEndTimeSelector: '#date-rule-end-time',
       DateRulePriceTypeSelector: '#date-rule-price-type',
       DateRuleUpdateAsSelector: '#date-rule-update-as',
       DateRuleUpdatePriceSelector: '#date-rule-updated-price',
       DateRuleModalDateSelector: '#date-rule-modal-date',
       DateRulesDateSelector: '#date-rules-date',
       DateRuleAvailabilityHoursSelector: '#date-availability-hours',
       DateRuleAppliedRules: '#date-applied-rules',
       DateAvailabilityRules: JSON.parse('{$localRulesJson}'),
       DateRulesListTitle: '#date-rules-list-title',
       DateRulesList: '#date-rules-list',
       DateRuleAddSelector: '#add-date-rule',
       DateRulesInputSelector: '#{$dateRulesSelector}',
       /*   =======================================================  */
    });
JS;


$this->registerJs($js);

?>