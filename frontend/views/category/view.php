<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Category */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-view">

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
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
            'slug',
            'description',
            [
                'label' => 'image',
                'value' => function ($model) {

                    if (empty($model->image)) {
                        return null;
                    }

                    /** @var $model \common\models\Category */
                    return \yii\helpers\Html::img($model->getImageFileUrl('image'), ['class' => 'thumbnail', 'width' => '100']);
                },
                'format' => 'html'
            ],
            [
                'attribute' => 'parent_id',
                'value' => function ($model) {
                    if (!$model->parent) {
                        return null;
                    }

                    return $model->parent->name;
                }
            ],
            [
                'label' => 'Cities',
                'value' => implode(',', $model->getSelectedCities()),
            ],
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
