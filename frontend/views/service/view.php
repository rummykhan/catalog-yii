<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Service */
/* @var $service \common\forms\AttachAttribute */

$this->title = $service->name;
$this->params['breadcrumbs'][] = ['label' => 'Services', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="service-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $service->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Add Fields', ['/service/attach-attribute', 'id' => $service->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Pricing Matrix', ['/service/add-pricing', 'id' => $service->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Manage Price Groups', ['/service/set-pricing-groups', 'id' => $service->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('View / Set Fields Dependency', ['/service/add-attribute-dependency', 'id' => $service->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $service->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <div class="row">
        <?= $this->render('service-view', ['service' => $service]) ?>
    </div>

    <?= $this->render('../common/service-attributes', ['model' => $service]) ?>


</div>
