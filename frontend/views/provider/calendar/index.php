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

$this->title = "Calendars";
$this->params['breadcrumbs'][] = ['label' => 'Providers', 'url' => ['/provider/index']];
$this->params['breadcrumbs'][] = ['label' => $model->username, 'url' => ['/provider/view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = ['label' => $this->title];
?>

<p>
    <a href="<?= Url::to(['/provider/calendar', 'provider_id' => $model->id]) ?>" class="btn btn-primary">Add
        Calendar</a>
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
                    'label' => 'Actions',
                    'value' => function ($model) {
                        $actions = '
                        <div class="btn-group">
                            <a href="' . Url::to(['/provider/calendar', 'calendar_id' => $model->id, 'provider_id' => $model->provider_id]) . '" class="btn btn-primary btn-sm">Edit</a>
                            <a href="' . Url::to(['/provider/delete-calendar', 'calendar_id' => $model->id, 'provider_id' => $model->provider_id]) . '" class="btn btn-default btn-sm">Delete</a>
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
