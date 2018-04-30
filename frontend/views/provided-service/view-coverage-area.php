<?php

use common\models\ServiceType;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use common\models\ProvidedServiceAreaSearch;

/* @var $this yii\web\View */
/* @var $model \common\forms\AddCoverageArea */
/* @var $type string */
/** @var ActiveDataProvider $dataProvider */
/** @var ProvidedServiceAreaSearch $searchModel */

$this->title = 'View Coverage';
$this->params['breadcrumbs'][] = ['label' => $model->provider->username, 'url' => ['/provider/view', 'id' => $model->provider_id]];
$this->params['breadcrumbs'][] = ['label' => 'Provided Services', 'url' => ['/provided-service/index', 'provider_id' => $model->provider_id]];
$this->params['breadcrumbs'][] = ['label' => $model->service->name, 'url' => ['/provided-service/view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;

$provider = $model->provider;
$service = $model->service;

?>

<a href="<?= Url::to(['/provided-service/add-type', 'id' => $model->id]) ?>" class="btn btn-primary">Add Service Type</a>
<a href="<?= Url::to(['/provided-service/add-coverage-area', 'id' => $model->id]) ?>" class="btn btn-primary">Add Coverage Area</a>
<br><br>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        ['class' => '\yii\grid\SerialColumn'],
        [
            'label' => 'Area Name',
            'value' => function ($model) {
                return Html::a($model['name'], [
                    '/provided-service/add-coverage-area',
                    'id' => $model['provided_service_id'],
                    'type' => $model['service_type_id'],
                    'area' => $model['id']
                ]);
            },
            'format' => 'html'
        ],
        'city',
        'type',
        [
            'label' => 'Actions',
            'value' => function ($model) {
                return Html::a('Add Pricing', [
                    '/provided-service/add-pricing', 'id' => $model['provided_service_id'], 'area' => $model['id'], 'type' => $model['service_type_id']
                ], ['class' => 'btn btn-primary btn-sm']);
            },
            'format' => 'html'
        ]
    ]
]) ?>
