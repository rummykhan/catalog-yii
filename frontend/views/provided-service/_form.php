<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model common\models\ProvidedService */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="provided-service-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="form-group">
        <label for="">Select Services</label>
        <?= Select2::widget([
            'model' => $model,
            'attribute' => 'service_id',
            'data' => $model->getUnProvidedServicesList(),
            'options' => ['placeholder' => 'Select Services'],
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
