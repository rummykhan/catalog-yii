<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\PriceType */

$this->title = 'Update Price Type: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => 'Price Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="price-type-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
