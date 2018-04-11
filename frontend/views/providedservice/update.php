<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ProvidedService */

$this->title = 'Update Provided Service: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => 'Provided Services', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="provided-service-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
