<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model common\models\Category */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="category-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <label for="">Select Parent Category</label>
        <?= Select2::widget([
            'model' => $model,
            'attribute' => 'parent_id',
            'data' => collect(\common\models\Category::find()->all())->pluck('name', 'id')->toArray(),
            'value' => $model->parent_id,
            'options' => ['placeholder' => 'Select parent category']
        ]) ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
