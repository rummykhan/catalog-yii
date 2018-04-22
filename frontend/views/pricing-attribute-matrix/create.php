<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\PricingAttributeMatrix */

$this->title = 'Create Pricing Attribute Matrix';
$this->params['breadcrumbs'][] = ['label' => 'Pricing Attribute Matrices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pricing-attribute-matrix-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
