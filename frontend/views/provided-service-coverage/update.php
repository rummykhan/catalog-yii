<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ProvidedServiceCoverage */

$this->title = 'Update Provided Service Coverage: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => 'Provided Service Coverages', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="provided-service-coverage-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
