<?php

use yii\helpers\Html;
use kartik\select2\Select2;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $service common\models\Service */
/* @var $model \common\forms\AttachAttribute */

$this->title = 'Attach attributes';
$this->params['breadcrumbs'][] = ['label' => 'Services', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $service->name, 'url' => ['view', 'id' => $service->id]];
$this->params['breadcrumbs'][] = 'Attach Attribute';
?>

<?php ActiveForm::begin([
    'action' => ['/service/attach-attribute', 'id' => $service->id]
]) ?>

<div class="form-group">
    <label for="">
        Select Attributes <a href="<?= \yii\helpers\Url::to([
            '/attribute/create', 'returnTo' => \yii\helpers\Url::to(['/service/attach-attribute', 'id' => $service->id])
        ]) ?>">or Create Attributes</a>
    </label>
    <?= Select2::widget([
        'model' => $model,
        'attribute' => 'attribute_id',
        'data' => \common\models\Attribute::toList(),
        'options' => ['placeholder' => 'Select attributes to attach'],
        'pluginOptions' => [
            'multiple' => false,
            'allowClear' => true
        ]
    ]) ?>
</div>

<div class="form-group">
    <label for="">Input type to be rendered</label>
    <?= Select2::widget([
        'model' => $model,
        'attribute' => 'input_type',
        'data' => \common\models\InputType::toList(),
        'options' => ['placeholder' => 'Select type of input'],
        'pluginOptions' => [
            'multiple' => false,
            'allowClear' => true
        ]
    ]) ?>
</div>

<div class="form-group">
    <label for="">User input</label>
    <?= Select2::widget([
        'model' => $model,
        'attribute' => 'user_input_type',
        'data' => \common\models\UserInputType::toList(),
        'options' => ['placeholder' => 'Select attributes to attach'],
        'pluginOptions' => [
            'multiple' => false,
            'allowClear' => true
        ]
    ]) ?>
</div>

<div class="form-group">
    <?= Html::submitButton('Attach', ['class' => 'btn btn-primary']) ?>
</div>


<?php ActiveForm::end() ?>




<?= $this->render('../common/service-attributes', ['model' => $service]) ?>
