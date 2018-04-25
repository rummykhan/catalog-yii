<?php

use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Service */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="service-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <label for="">Select Category</label>
        <?= Select2::widget([
            'model' => $model,
            'attribute' => 'category_id',
            'data' => collect(\common\models\Category::find()->all())->pluck('name', 'id')->toArray(),
            'value' => $model->category_id,
            'options' => ['placeholder' => 'Select parent category']
        ]) ?>
    </div>

    <div class="form-group">
        <label for="">Select Cities</label>
        <?= Select2::widget([
            'name' => 'cities',
            'data' => \common\models\City::toList(),
            'value' => collect($model->getCities()->asArray()->all())->pluck('id', 'id')->toArray(),
            'options' => ['placeholder' => 'Select city'],
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
