<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model common\models\AttributeOption */
/** @var $attribute \common\models\Attribute */
/** @var $service \common\models\Service */

$this->title = 'Create Attribute Option for ' . $attribute->name;
if ($service) {
    $this->params['breadcrumbs'][] = ['label' => $service->name, 'url' => ['/service/view', 'id' => $service->id]];
}
if ($attribute) {
    $this->params['breadcrumbs'][] = ['label' => $attribute->name, 'url' => ['/attribute/view', 'id' => $attribute->id]];
} else {
    $this->params['breadcrumbs'][] = ['label' => 'Attribute Options', 'url' => ['index']];
}
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="attribute-option-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin([
        'action' => ['/attribute-option/create', 'attribute_id' => $attribute->id, 'service_id' => $service->id]
    ]); ?>

    <?= $this->render('_form', [
        'model' => $model,
        'form' => $form
    ]) ?>

    <?php ActiveForm::end(); ?>

</div>
