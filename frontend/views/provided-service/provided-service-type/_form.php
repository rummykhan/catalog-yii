<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\select2\Select2;
use common\models\ProvidedServiceType;

/* @var $model ProvidedServiceType */

?>


<div class="row">
    <div class="col-md-6">

        <?php $form = ActiveForm::begin() ?>

        <div class="form-group">
            <label for="">Select Calendar</label>
            <?= Select2::widget([
                'model' => $model,
                'attribute' => 'calendar_id',
                'data' => $model->providedService->provider->getCalendarsList(),
                'options' => ['placeholder' => 'Select Calendar']
            ]) ?>
        </div>

        <div class="form-group">
            <label for="">Select Service Area</label>
            <?= Select2::widget([
                'model' => $model,
                'attribute' => 'service_area_id',
                'data' => $model->providedService->provider->getServiceAreasList(),
                'options' => ['placeholder' => 'Select Service Area']
            ]) ?>
        </div>

        <div class="form-group">
            <label for="">Select Request Type</label>
            <?= Select2::widget([
                'model' => $model,
                'attribute' => 'service_request_type_id',
                'data' => $model->providedService->service->getRequestTypesList(),
                'options' => ['placeholder' => 'Select Request Type']
            ]) ?>
        </div>

        <div class="form-group">
            <button class="btn btn-primary">Submit</button>
        </div>


        <?php ActiveForm::end() ?>

    </div>


</div>
