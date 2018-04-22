<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ProvidedServiceBasePricing */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="provided-service-base-pricing-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'provided_service_id')->textInput() ?>

    <?= $form->field($model, 'pricing_attribute_id')->textInput() ?>

    <?= $form->field($model, 'base_price')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
