<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ServiceAttributeDependsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Service Attribute Depends';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="service-attribute-depends-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Service Attribute Depends', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'service_attribute_id',
            'depends_on_id',
            'service_attribute_option_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>