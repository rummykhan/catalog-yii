<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UserInputType */

$this->title = 'Update User Input Type: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => 'User Input Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-input-type-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
