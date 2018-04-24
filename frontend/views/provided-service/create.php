<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ProvidedService */

$this->title = 'Create Provided Service';
$this->params['breadcrumbs'][] = ['label' => 'Provided Services', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="provided-service-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
