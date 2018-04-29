<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ProvidedService */
/* @var $provider common\models\Provider */


$this->title = 'Create Provided Service';
$this->params['breadcrumbs'][] = ['label' => $provider->username, 'url' => ['/provider/view', 'id' => $provider->id]];
$this->params['breadcrumbs'][] = ['label' => 'Provided Services', 'url' => ['/provided-service/index', 'provider_id' => $provider->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="provided-service-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
