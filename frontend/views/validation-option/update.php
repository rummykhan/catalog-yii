<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ValidationOption */

$this->title = 'Update Validation Option: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => 'Validation Options', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="validation-option-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
