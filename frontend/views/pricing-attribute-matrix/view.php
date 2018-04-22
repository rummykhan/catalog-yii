<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\PricingAttributeMatrix */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Pricing Attribute Matrices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pricing-attribute-matrix-view">

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
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'pricing_attribute_parent_id',
            'service_attribute_option_id',
        ],
    ]) ?>

</div>
