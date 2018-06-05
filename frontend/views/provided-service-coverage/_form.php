<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ServiceAreaCoverage */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="provided-service-coverage-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'provided_service_area_id')->textInput() ?>

    <?= $form->field($model, 'lat')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'lng')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
