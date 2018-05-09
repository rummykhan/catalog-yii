<?php

use kartik\select2\Select2;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\CategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Categories';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-index">

    <p>
        <?= Html::a('Create Category', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            [
                'attribute' => 'parent_id',
                'value' => function($model){

                    if(!$model->parent){
                        return null;
                    }

                    return $model->parent->name;
                },
                'filter' => Select2::widget([
                    'model' => $searchModel,
                    'attribute' => 'parent_id',
                    'data' => collect(\common\models\Category::find()->all())->pluck('name', 'id')->toArray(),
                    'value' => $searchModel->parent_id,
                    'options' => ['placeholder' => 'Select parent category'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ]
                ])
            ],
            'created_at',
            'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
