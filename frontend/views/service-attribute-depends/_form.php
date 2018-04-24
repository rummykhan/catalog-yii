<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ServiceAttributeDepends */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="service-attribute-depends-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'service_attribute_id')->textInput() ?>

    <?= $form->field($model, 'depends_on_id')->textInput() ?>

    <?= $form->field($model, 'service_attribute_option_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
