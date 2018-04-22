<?php

use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Attribute */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="attribute-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <label for="">Select Type of attribute</label>
        <?= Select2::widget([
            'model' => $model,
            'attribute' => 'type',
            'data' => collect(\common\models\AttributeType::find()->all())->pluck('name', 'id')->toArray(),
            'value' => $model->type,
            'options' => ['placeholder' => 'Select attribute type']
        ]) ?>
    </div>

    <div class="form-group">
        <label for="">Select Type of input attribute</label>
        <?= Select2::widget([
            'model' => $model,
            'attribute' => 'input_type',
            'data' => collect(\common\models\AttributeInputType::find()->all())->pluck('name', 'id')->toArray(),
            'value' => $model->input_type,
            'options' => ['placeholder' => 'Select attribute input type']
        ]) ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
