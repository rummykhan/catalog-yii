<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ServiceCity */

$this->title = 'Create Service City';
$this->params['breadcrumbs'][] = ['label' => 'Service Cities', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="service-city-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
