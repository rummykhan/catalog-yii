<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ProvidedService */

$this->title = $model->service->name;
$this->params['breadcrumbs'][] = ['label' => $model->provider->username, 'url' => ['/provider/view', 'id' => $model->provider_id]];
$this->params['breadcrumbs'][] = ['label' => 'Provided Services', 'url' => ['/provided-service/index', 'provider_id' => $model->provider_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="provided-service-view">

    <p>
        <?= Html::a('View Coverage Areas', ['/provided-service/view-coverage-areas', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <a href="<?= Url::to(['/provided-service/add-type', 'id' => $model->id]) ?>" class="btn btn-primary">Add Service Type</a>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'label' => 'Service',
                'value' => function($model){
                    /**@var $model \common\models\ProvidedService */
                    return $model->service->name;
                }
            ],
            [
                'label' => 'Service Type',
                'value' => function($model){
                    /**@var $model \common\models\ProvidedService */
                    return implode(',', collect($model->getProvidedServiceTypes()->select(['type'])->asArray()->all())->pluck('type')->toArray());
                }
            ],
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
