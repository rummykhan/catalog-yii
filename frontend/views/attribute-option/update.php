<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\AttributeOption */

$this->title = 'Update Attribute Option: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => 'Attribute Options', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="attribute-option-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
