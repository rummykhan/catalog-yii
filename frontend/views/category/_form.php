<?php

use common\helpers\MultilingualInputHelper;
use common\models\Category;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model common\models\Category */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="category-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">

            <?= MultilingualInputHelper::textInputs($form, $model, 'name') ?>

            <?= MultilingualInputHelper::textareaInputs($form, $model, 'description') ?>

            <div class="form-group">
                <label for="">Select Parent Category</label>
                <?= Select2::widget(array(
                    'model' => $model,
                    'attribute' => 'parent_id',
                    'data' => Category::toList(),
                    'value' => $model->parent_id,
                    'options' => array('placeholder' => 'Select parent category')
                )) ?>
            </div>

            <?= $form->field($model, 'active')->checkbox() ?>

        </div>

        <div class="col-md-6">

            <?php if (!empty($model->image)) { ?>
                <img src="<?= $model->getImageFileUrl('image') ?>" alt="" width="150" class="thumbnail">
            <?php } ?>

            <?= $form->field($model, 'image')->fileInput() ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
