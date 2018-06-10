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

