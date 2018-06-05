<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ServiceAreaCoverage */

$this->title = 'Create Provided Service Coverage';
$this->params['breadcrumbs'][] = ['label' => 'Provided Service Coverages', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="provided-service-coverage-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
