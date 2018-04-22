<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\PricingAttributeParent */

$this->title = 'Create Pricing Attribute Parent';
$this->params['breadcrumbs'][] = ['label' => 'Pricing Attribute Parents', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pricing-attribute-parent-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
