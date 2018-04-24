<?php

use yii\helpers\Html;
use kartik\select2\Select2;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \common\models\Service */
/* @var $formModel \common\forms\AddPricingAttribute */

$this->title = 'Set pricing attributes';
$this->params['breadcrumbs'][] = ['label' => 'Services', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['/service/view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<p>
    <a href="<?= \yii\helpers\Url::to(['/service/view-price-matrix', 'id' => $model->id]) ?>"
       class="btn btn-primary">
        View Price Matrix
    </a>
</p>

<?php $form = ActiveForm::begin([
    'action' => ['/service/add-pricing-attribute', 'id' => $model->id],
    'method' => 'POST'
]) ?>

<div class="row">

    <div class="col-md-6">
        <div class="form-group">
            <label>Select attributes</label>

            <?= Select2::widget([
                'model' => $formModel,
                'attribute' => 'service_attributes',
                'data' => $model->getServiceAttributesListNotInPriceGroup(),
                'value' => $formModel->service_attributes,
                'options' => ['placeholder' => 'Select attribute'],
                'pluginOptions' => [
                    'multiple' => true,
                    'allowClear' => true
                ]
            ]) ?>

        </div>
    </div>

    <div class="col-md-6">
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>Select Price Type</label>
            <?= Select2::widget([
                'model' => $formModel,
                'attribute' => 'price_type_id',
                'data' => \common\models\PriceType::toList(),
                'value' => $formModel->price_type_id,
                'options' => ['placeholder' => 'Select price type'],
                'pluginOptions' => [
                    'multiple' => false,
                    'allowClear' => true
                ]
            ]) ?>
        </div>
    </div>
</div>


<hr>

<div class="form-group">
    <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
</div>


<?php ActiveForm::end() ?>


<hr>
<div class="row">
    <div class="col-md-6">
        <?php foreach ($model->pricingAttributeGroups as $pricingAttributeGroup) { ?>
            <h4><?= ucwords($pricingAttributeGroup->name) ?> attributes</h4>
            <ul class="list-group">
                <?php foreach ($pricingAttributeGroup->pricingAttributes as $pricingAttribute) { ?>
                    <li class="list-group-item"><?= $pricingAttribute->serviceAttribute->attribute0->name ?></li>
                <?php } ?>
            </ul>
            <hr>
        <?php } ?>
    </div>
</div>