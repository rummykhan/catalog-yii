<?php

use yii\web\View;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;
use kartik\select2\Select2;

/** @var View $this */
/** @var \common\models\ProvidedService $providedService */
/** @var \common\forms\AddType $model */

$this->title = 'Add Service Types';

$this->params['breadcrumbs'][] = ['label' => $providedService->provider->username, 'url' => ['/provider/view', 'id' => $providedService->provider_id]];
$this->params['breadcrumbs'][] = ['label' => 'Provided Services', 'url' => ['/provided-service/index', 'provider_id' => $providedService->provider_id]];
$this->params['breadcrumbs'][] = ['label' => $providedService->service->name, 'url' => ['/provided-service/view', 'id' => $providedService->id]];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-md-6">

        <?php $form = ActiveForm::begin() ?>

        <div class="form-group">
            <label for="">Select Service Types</label>
            <?= Select2::widget([
                'model' => $model,
                'attribute' => 'service_types',
                'data' => \common\models\ServiceType::toList(),
                'options' => ['placeholder' => 'Select service type'],
                'pluginOptions' => [
                    'multiple' => true,
                    'allowClear' => true
                ]
            ]) ?>
        </div>

        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end() ?>
    </div>
</div>
