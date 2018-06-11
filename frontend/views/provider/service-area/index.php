<?php

use yii\web\View;
use yii\helpers\Url;
use common\models\CalendarSearch;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;

/** @var $this View */
/** @var $model \common\models\Provider */
/** @var $searchModel CalendarSearch */
/** @var $dataProvider ActiveDataProvider */

$this->title = "Service Area";
$this->params['breadcrumbs'][] = ['label' => 'Providers', 'url' => ['/provider/index']];
$this->params['breadcrumbs'][] = ['label' => $model->username, 'url' => ['/provider/view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = ['label' => $this->title];
?>

<p>
    <a href="<?= Url::to(['/provider/cu-service-area', 'provider_id' => $model->id]) ?>" class="btn btn-primary">
        Add Service Area
    </a>
</p>

<div class="row">
    <div class="col-md-12">

        <?= GridView::widget([
            'filterModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'columns' => [
                'id',
                'name',
                [
                    'label' => 'City',
                    'value' => function($model){
                        return $model->city->name;
                    }
                ],
                [
                    'label' => 'Actions',
                    'value' => function ($model) {
                        $actions = '
                        <div class="btn-group">
                            <a href="' . Url::to(['/provider/cu-service-area', 'area_id' => $model->id, 'provider_id' => $model->provider_id]) . '" class="btn btn-primary btn-sm">Edit</a>
                            <a href="' . Url::to(['/provider/delete-service-area', 'area_id' => $model->id, 'provider_id' => $model->provider_id]) . '" class="btn btn-default btn-sm">Delete</a>
                        </div>
                    ';

                        return $actions;
                    },
                    'format' => 'html'
                ]
            ]
        ]) ?>

    </div>
</div>

