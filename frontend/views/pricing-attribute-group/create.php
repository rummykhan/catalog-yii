<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\PricingAttributeGroup */

$this->title = 'Create Pricing Attribute Group';
$this->params['breadcrumbs'][] = ['label' => 'Pricing Attribute Groups', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pricing-attribute-group-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
