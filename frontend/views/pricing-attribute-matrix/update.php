<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\PricingAttributeMatrix */

$this->title = 'Update Pricing Attribute Matrix: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => 'Pricing Attribute Matrices', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="pricing-attribute-matrix-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
