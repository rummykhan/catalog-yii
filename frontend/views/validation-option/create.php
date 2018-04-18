<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ValidationOption */

$this->title = 'Create Validation Option';
$this->params['breadcrumbs'][] = ['label' => 'Validation Options', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="validation-option-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
