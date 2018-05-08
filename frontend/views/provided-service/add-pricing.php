<?php

use common\helpers\ServiceAttributeMatrix;
use common\models\ProvidedService;
use common\models\ProvidedServiceArea;
use common\models\ProvidedServiceType;
use common\models\Provider;
use common\models\Service;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $view int */
/* @var $model ProvidedService */
/* @var $provider Provider */
/* @var $service Service */
/* @var $type string */
/** @var $area ProvidedServiceArea */
/** @var $providedServiceType ProvidedServiceType */
/* @var $motherMatrix ServiceAttributeMatrix */

$this->title = 'Add Pricing for ' . $area->name;
$this->params['breadcrumbs'][] = ['label' => $provider->username, 'url' => ['/provider/view', 'id' => $model->provider_id]];
$this->params['breadcrumbs'][] = ['label' => 'Provided Services', 'url' => ['/provided-service/index', 'provider_id' => $model->provider_id]];
$this->params['breadcrumbs'][] = ['label' => $service->name, 'url' => ['/provided-service/view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = ['label' => $providedServiceType->serviceType->type];
$this->params['breadcrumbs'][] = ['label' => 'Coverage Areas', 'url' => ['/provided-service/view-coverage-areas', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-md-12">
        <a href="<?= Url::to([
            '/provided-service/set-pricing',
            'id' => $model->id,
            'area' => $area->id,
            'type' => $type,
            'view' => 1
        ]) ?>"
           class="btn <?= $view == 1 ? 'btn-primary' : 'btn-default' ?>">Basic</a>
        <a href="<?= Url::to([
            '/provided-service/set-pricing',
            'id' => $model->id,
            'area' => $area->id,
            'type' => $type,
            'view' => 2
        ]) ?>"
           class="btn <?= $view == 2 ? 'btn-primary' : 'btn-default' ?>">Legacy</a>
        <a href="<?= Url::to([
            '/provided-service/set-pricing',
            'id' => $model->id,
            'area' => $area->id,
            'type' => $type,
            'view' => 3
        ]) ?>"
           class="btn <?= $view == 3 ? 'btn-primary' : 'btn-default' ?>">Dropdown</a>
    </div>
</div>

<br>

<?php foreach ($motherMatrix->getMatrices() as $index => $matrix) { ?>
    <div class="panel panel-default">
        <div class="panel-heading">Group - <?= $index ?></div>
        <div class="panel-body">
            <?= $this->render('price-matrix', [
                'model' => $model,
                'area' => $area,
                'type' => $providedServiceType->service_type_id,
                'matrixHeaders' => $matrix->getMatrixHeaders(),
                'matrixRows' => $matrix->getMatrixRows(),
                'noImpactRows' => $matrix->getNoImpactRows(),
                'independentRows' => $matrix->getIndependentRows(),
                'incremental' => $matrix->getIncrementalAttributes(),
                'attributeGroups' => $matrix->getAttributesGroup(),
                'view' => $view
            ]) ?>
        </div>
    </div>
<?php } ?>
