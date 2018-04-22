<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ProvidedServiceBasePricing */

$this->title = 'Create Provided Service Base Pricing';
$this->params['breadcrumbs'][] = ['label' => 'Provided Service Base Pricings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="provided-service-base-pricing-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
