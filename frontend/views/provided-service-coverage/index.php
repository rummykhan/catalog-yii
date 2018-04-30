<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ProvidedServiceCoverageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Provided Service Coverages';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="provided-service-coverage-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Provided Service Coverage', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'provided_service_area_id',
            'lat',
            'lng',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
