<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\PricingAttributeParent */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pricing-attribute-parent-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'service_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
