<?php

use yii\web\View;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;

/* @var View $this */
/** @var \common\models\ServiceAttribute $attribute */
/** @var \common\models\Service $service */
/** @var \common\forms\ImportOptionsFromExcel $model */

$this->title = 'Import Options';
$this->params['breadcrumbs'][] = ['label' => 'Services', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $service->name, 'url' => ['/service/view', 'id' => $service->id]];
$this->params['breadcrumbs'][] = ['label' => $attribute->name, 'url' => ['/service/edit-attribute', 'id' => $service->id, 'attribute_id' => $attribute->id]];
$this->params['breadcrumbs'][] = $this->title;

?>


<div class="row">
    <div class="col-md-12">


        <div class="row">
            <div class="col-md-4">

                <?php $form = ActiveForm::begin() ?>

                <?= $form->field($model, 'file')->fileInput(['class' => 'form-control']) ?>

                <div class="form-group">
                    <button class="btn btn-primary">Submit</button>
                </div>

            </div>

            <div class="col-md-8">
                <h4>Excel Format Sample</h4>
                <table class="table table-formatted">
                    <thead>
                    <tr>
                        <th>name</th>
                        <th>name-ar</th>
                        <th>description</th>
                        <th>description-ar</th>
                        <th>mobile-description</th>
                        <th>mobile-description-ar</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>black</td>
                        <td>أسود</td>
                        <td>Color of mobile</td>
                        <td>لون الجوال</td>
                        <td>Color of mobile</td>
                        <td>لون الجوال</td>
                    </tr>
                    </tbody>
                </table>
            </div>

        </div>

        <?php ActiveForm::end() ?>

    </div>
</div>

