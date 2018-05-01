<?php

use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $providedService common\models\ProvidedService */
/* @var $model \common\forms\AddType */

$this->title = 'Update Provided Service:';
$this->params['breadcrumbs'][] = ['label' => $providedService->provider->username, 'url' => ['/provider/view', 'id' => $providedService->provider_id]];
$this->params['breadcrumbs'][] = ['label' => 'Provided Services', 'url' => ['/provided-service/index', 'provider_id' => $providedService->provider_id]];
$this->params['breadcrumbs'][] = ['label' => $providedService->service->name, 'url' => ['view', 'id' => $providedService->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="provided-service-update">

    <?php $form = ActiveForm::begin(); ?>

    <input type="hidden" name="provider_id" value="<?= $providedService->provider_id ?>">

    <div class="form-group">
        <label for="">Select Service Types</label>
        <?= Select2::widget([
            'model' => $model,
            'attribute' => 'service_types',
            'data' => \common\models\ServiceType::toList(),
            'value' => $model->service_types,
            'options' => ['placeholder' => 'Select service type'],
            'pluginOptions' => [
                'multiple' => true,
                'allowClear' => true
            ]
        ]) ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
