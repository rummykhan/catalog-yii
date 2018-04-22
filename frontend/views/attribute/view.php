<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Attribute */
/* @var $service common\models\Service */

$this->title = $model->name;
if($service){
    $this->params['breadcrumbs'][] = ['label' => $service->name, 'url' => ['/service/view', 'id' => $service->id]];
}else{
    $this->params['breadcrumbs'][] = ['label' => 'Attributes', 'url' => ['index']];
}
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="attribute-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('Add Options', ['/attribute-option/create', 'attribute_id' => $model->id, 'service_id' => $service->id],
            [
                    'class' => 'btn btn-primary'
            ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'type',
            'input_type',
            'created_at',
            'updated_at',
        ],
    ]) ?>

    <br>

    <table class="table table-striped table-responsive">
        <thead>
            <tr>
                <td>ID</td>
                <td>Name</td>
                <td>Created</td>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($model->attributeOptions as $attributeOption) { ?>
            <tr>
                <td><?= $attributeOption->id ?></td>
                <td><?= $attributeOption->name ?></td>
                <td><?= $attributeOption->created_at ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

</div>
