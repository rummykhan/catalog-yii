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
        <?= Html::a('Add Attribute', ['/attribute/create', 'service_id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Set Pricing', ['/service/pricing', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
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
            'created_at',
            'updated_at',
        ],
    ]) ?>

    <br>

    <table class="table table-striped table-responsive">
        <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
        </tr>
        </thead>
        <tbody>
        <?php /** @var \common\models\Attribute $attribute */
        foreach ($model->serviceAttributes as $attribute) { ?>
            <tr>
                <td><?= $attribute->id ?></td>
                <td>
                    <a href="<?= \yii\helpers\Url::to(['/attribute/view', 'id' => $attribute->id, 'service_id' => $model->id]) ?>">
                        <?= $attribute->name ?>
                    </a>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>


</div>
