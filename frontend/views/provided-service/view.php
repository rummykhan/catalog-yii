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
        <?php if ($model->getProvidedRequestTypes()->count() > 0) { ?>
            <a href="<?= Url::to(['/provided-service/view-coverage-areas', 'id' => $model->id]) ?>"
               class="btn btn-primary">
                View / Set Coverage Areas
            </a>
        <?php } ?>
    </p>

    <div class="row">
        <div class="col-md-6">
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
                    'created_at',
                    'updated_at',
                ],
            ]) ?>


            <?php \yii\widgets\ActiveForm::begin(['method' => 'POST', 'action' => ['/provided-service/update', 'id' => $model->id]]) ?>

            <?php foreach ($model->service->getActiveRequestTypes() as $serviceType) { ?>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <input type="checkbox"
                            <?= $model->getProvidedRequestTypes()
                                ->andWhere(['service_request_type_id' => $serviceType->request_type_id])
                                ->count() > 0 ? 'checked' : '' ?>
                               name="service_type[<?= $serviceType->id ?>]"
                               value="<?= $serviceType->id ?>">
                        <?= $serviceType->requestType->name ?>
                    </div>
                </div>
            <?php } ?>

            <button class="btn btn-primary">Submit</button>

            <?php \yii\widgets\ActiveForm::end() ?>

        </div>
    </div>

    <br>

</div>
