<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\PricingAttribute */

$this->title = 'Update Pricing Attribute: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => 'Pricing Attributes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="pricing-attribute-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
