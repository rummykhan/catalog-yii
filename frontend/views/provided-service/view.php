<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ProvidedService */

$this->title = $model->service->name;
$this->params['breadcrumbs'][] = ['label' => $model->provider->username, 'url' => ['/provider/view', 'id' => $model->provider_id]];
$this->params['breadcrumbs'][] = ['label' => 'Provided Services', 'url' => ['/provided-service/index', 'provider_id' => $model->provider_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="provided-service-view">

    <p>
        <?php if ($model->getProvidedServiceTypes()->count() > 0) { ?>
            <a href="<?= Url::to(['/provided-service/view-coverage-areas', 'id' => $model->id]) ?>"
               class="btn btn-primary">
                View / Set Coverage Areas
            </a>
        <?php } ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'label' => 'Service',
                'value' => function ($model) {
                    /**@var $model \common\models\ProvidedService */
                    return $model->service->name;
                }
            ],
            [
                'label' => 'Service Type',
                'value' => function ($model) {
                    /**@var $model \common\models\ProvidedService */
                    return implode(',', collect($model->getProvidedServiceTypes()->select(['type'])->asArray()->all())->pluck('type')->toArray());
                }
            ],
            'created_at',
            'updated_at',
        ],
    ]) ?>

    <br>

    <?php \yii\widgets\ActiveForm::begin(['method' => 'POST', 'action' => ['/provided-service/update', 'id' => $model->id]]) ?>

    <?php foreach (\common\models\ServiceType::find()->all() as $serviceType) { ?>

        <div class="form-group">
            <input type="checkbox"
                   <?= \common\models\ProvidedServiceType::find()
                       ->where(['provided_service_id' => $model->id])
                       ->andWhere(['service_type_id' => $serviceType->id])
                       ->andWhere(['deleted' => false])
                       ->count() > 0 ? 'checked' : '' ?>
                   name="service_type[<?= $serviceType->id ?>]"
                   value="<?= $serviceType->id ?>"> <?= $serviceType->type ?>
        </div>
    <?php } ?>

    <button class="btn btn-primary">Submit</button>

    <?php \yii\widgets\ActiveForm::end() ?>

</div>
