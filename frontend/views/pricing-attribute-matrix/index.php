<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\PricingAttributeMatrixSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Pricing Attribute Matrices';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pricing-attribute-matrix-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Pricing Attribute Matrix', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'pricing_attribute_parent_id',
            'service_attribute_option_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
