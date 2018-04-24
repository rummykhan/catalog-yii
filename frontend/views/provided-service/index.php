<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ProvidedServiceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Provided Services';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="provided-service-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <a href="<?= \yii\helpers\Url::to(['/provided-service/create', 'provider_id' => $searchModel->provider_id]) ?>" class="btn btn-success">Provide New Service</a>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'service_id',
            'provider_id',
            'created_at',
            'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
