<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ProvidedService */
/* @var $searchModel \common\models\ProvidedServiceTypeSearch */
/* @var $dataProvider \yii\data\ActiveDataProvider */

$this->title = $model->service->name;
$this->params['breadcrumbs'][] = ['label' => $model->provider->username, 'url' => ['/provider/view', 'id' => $model->provider_id]];
$this->params['breadcrumbs'][] = ['label' => 'Provided Services', 'url' => ['/provided-service/index', 'provider_id' => $model->provider_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="provided-service-view">

    <p>
        <a href="<?= Url::to(['/provided-service/add-type', 'id' => $model->id]) ?>" class="btn btn-primary btn-sm">
            Add More
        </a>
    </p>

    <div class="row">
        <div class="col-md-12">
            <?= GridView::widget([
                'filterModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'columns' => [
                    [
                        'label' => 'Actions',
                        'value' => function ($model) {
                            $buttons = Html::a('Edit', ['/provided-service/edit-type', 'id' => $model['id']], ['class' => 'btn btn-primary btn-sm']);
                            $buttons .= ' '.Html::a('Add Pricing', ['/provided-service/set-pricing', 'id' => $model['id']], ['class' => 'btn btn-primary btn-sm']);

                            return $buttons;
                        },
                        'format' => 'html'
                    ],
                    'service_name',
                    'request_type',
                    'calendar_name',
                    'service_area',
                ]
            ]) ?>
        </div>
    </div>

    <br>

</div>
