<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ProvidedServiceArea */

$this->title = 'Create Provided Service Area';
$this->params['breadcrumbs'][] = ['label' => 'Provided Service Areas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="provided-service-area-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
