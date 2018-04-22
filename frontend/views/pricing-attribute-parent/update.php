<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\PricingAttributeParent */

$this->title = 'Update Pricing Attribute Parent: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => 'Pricing Attribute Parents', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="pricing-attribute-parent-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
