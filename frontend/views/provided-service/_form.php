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

    <input type="hidden" name="provider_id" value="<?= $model->provider_id ?>">

    <div class="form-group">
        <label for="">Select Services</label>
        <?= Select2::widget([
            'name' => 'services',
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
