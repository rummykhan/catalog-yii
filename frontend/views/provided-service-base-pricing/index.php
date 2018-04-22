<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ProvidedServiceBasePricingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Provided Service Base Pricings';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="provided-service-base-pricing-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Provided Service Base Pricing', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'provided_service_id',
            'pricing_attribute_id',
            'base_price',
            'created_at',
            //'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
