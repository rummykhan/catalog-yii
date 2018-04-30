<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ProvidedServiceArea */

$this->title = 'Update Provided Service Area: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => 'Provided Service Areas', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="provided-service-area-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
