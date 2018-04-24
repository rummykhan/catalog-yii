<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\PricingAttributeGroup */

$this->title = 'Update Pricing Attribute Group: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => 'Pricing Attribute Groups', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="pricing-attribute-group-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
