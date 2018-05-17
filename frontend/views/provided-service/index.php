<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ProvidedServiceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $provider \common\models\Provider */

$this->title = 'Provided Services';
$this->params['breadcrumbs'][] = ['label' => $provider->username, 'url' => ['/provider/view', 'id' => $provider->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="provided-service-index">

    <p>
        <a href="<?= \yii\helpers\Url::to(['/provided-service/create', 'provider_id' => $searchModel->provider_id]) ?>"
           class="btn btn-success">Provide New Service</a>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'label' => 'Service',
                'value' => function ($model) {
                    /**@var $model \common\models\ProvidedService */
                    return Html::a($model->service->name, ['/provided-service/view', 'id' => $model->id]);
                },
                'format' => 'html'
            ],
            'created_at',
        ],
    ]); ?>
</div>
