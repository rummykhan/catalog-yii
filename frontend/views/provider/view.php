<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Provider */

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => 'Providers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="provider-view">

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Provided Services', ['/provided-service', 'provider_id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Calendars', ['/provider/availability', 'provider_id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Service Areas', ['/provider/service-area', 'provider_id' => $model->id], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'username',
            'password',
            'email:email',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
