<?php

use yii\web\View;
use common\models\ProvidedServiceType;

/** @var $this View */
/** @var $model ProvidedServiceType */


$this->title = 'Edit Service Type';
$this->params['breadcrumbs'][] = ['label' => $model->providedService->provider->username, 'url' => ['/provider/view', 'id' => $model->providedService->provider->id]];
$this->params['breadcrumbs'][] = ['label' => 'Services', 'url' => ['/provided-service', 'provider_id' => $model->providedService->provider->id]];
$this->params['breadcrumbs'][] = ['label' => $model->providedService->service->name, 'url' => ['/provided-service/view', 'id' => $model->providedService->id]];
$this->params['breadcrumbs'][] = $this->title;

?>


<?= $this->render('_form', compact('model')) ?>
