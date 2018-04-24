<?php

use common\models\Validation;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\forms\AttachOption */
/* @var $attribute common\models\Attribute */
/* @var $service \common\models\Service */

$this->title = 'Attach options';
$this->params['breadcrumbs'][] = ['label' => $service->name, 'url' => ['/service/view', 'id' => $service->id]];
$this->params['breadcrumbs'][] = ['label' => $attribute->name];
$this->params['breadcrumbs'][] = 'Attach Options';
?>

<?php ActiveForm::begin([
    'action' => ['/attribute/attach-validation', 'service_id' => $service->id, 'attribute_id' => $attribute->id],
]) ?>

    <div class="form-group">
        <label for="">
            Select validation <a href="<?= \yii\helpers\Url::to([
                '/validation/create',
                'returnTo' => \yii\helpers\Url::to(['/attribute/attach-validation', 'service_id' => $service->id, 'attribute_id' => $attribute->id])
            ]) ?>">or Create Validation</a>
        </label>
        <?= Select2::widget([
            'model' => $model,
            'attribute' => 'validations',
            'data' => Validation::toList(),
            'options' => ['placeholder' => 'Select validations to attach'],
            'pluginOptions' => [
                'multiple' => true,
                'allowClear' => true
            ]
        ]) ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Attach', ['class' => 'btn btn-primary']) ?>
    </div>


<?php ActiveForm::end() ?>


<?= $this->render('../common/service-attributes-options', ['service' => $service, 'attribute' => $attribute]) ?>