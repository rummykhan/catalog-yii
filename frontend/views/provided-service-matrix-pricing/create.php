<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ProvidedServiceMatrixPricing */

$this->title = 'Create Provided Service Matrix Pricing';
$this->params['breadcrumbs'][] = ['label' => 'Provided Service Matrix Pricings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="provided-service-matrix-pricing-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
