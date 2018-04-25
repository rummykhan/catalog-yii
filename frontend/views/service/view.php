<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Service */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Services', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="service-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('View / Set Attribute', ['/service/attach-attribute', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('View / Set Pricing Attributes', ['/service/add-pricing', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('View / Set Attribute Dependency', ['/service/add-attribute-dependency', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            [
                'label' => 'category',
                'attribute' => 'parent_id',
                'value' => function ($model) {
                    if (!$model->category) {
                        return null;
                    }

                    return $model->category->name;
                }
            ],
            [
                'label' => 'Cities',
                'value' => function($model){
                    /** @var $model \common\models\Service */
                    return implode(',', collect($model->getCities()->asArray()->all())->pluck('name')->toArray());
                }
            ],
            'created_at',
            'updated_at',
        ],
    ]) ?>

    <?= $this->render('../common/service-attributes', ['model' => $model]) ?>


</div>
