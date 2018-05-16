<?php

use common\helpers\GlobalHelper;
use yii\helpers\Html;
use kartik\select2\Select2;
use yii\widgets\ActiveForm;
use common\helpers\MultilingualInputHelper;

/* @var $this yii\web\View */
/* @var $service common\models\Service */
/* @var $attribute common\models\ServiceAttribute */
/* @var $model \common\forms\AttachOptions */


$this->title = 'Add Field';
$this->params['breadcrumbs'][] = ['label' => 'Services', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $service->name, 'url' => ['/service/view', 'id' => $service->id]];
$this->params['breadcrumbs'][] = ['label' => $attribute->name, 'url' => ['/service/view', 'id' => $service->id]];
$this->params['breadcrumbs'][] = $this->title;

?>
<?php $form = ActiveForm::begin() ?>

<?php if ($attribute->fieldType->name !== \common\models\FieldType::TYPE_RANGE) { ?>

    <div class="row">
        <div class="col-md-6">
            <a href="<?= \yii\helpers\Url::to(['/service/import-excel', 'attribute_id' => $attribute->id, 'service_id' => $service->id]) ?>"
               class="btn btn-primary btn-sm">
                Import Options from Excel
            </a>
        </div>
    </div>

    <br>

<?php } ?>

    <div class="row">
        <div class="col-md-6">

            <?php if ($attribute->fieldType->name === \common\models\FieldType::TYPE_RANGE) { ?>

                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'min')->textInput(['type' => 'number']) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'max')->textInput(['type' => 'number']) ?>
                    </div>
                </div>

            <?php } else { ?>

                <div class="form-group">
                    <label for="">Add Field Values</label>
                    <?= Select2::widget([
                        'model' => $model,
                        'attribute' => 'service_attribute_options',
                        'value' => [],
                        'data' => [],
                        'maintainOrder' => true,
                        'options' => ['placeholder' => 'Add options', 'multiple' => true],
                        'pluginOptions' => [
                            'tags' => true,
                            'maximumInputLength' => 30
                        ],
                    ]); ?>
                </div>

                <?= $form->field($model, 'bulk')->textarea(['rows' => 10]) ?>

            <?php } ?>

        </div>

        <div class="col-md-6">

            <?php if ($attribute->fieldType->name !== \common\models\FieldType::TYPE_RANGE) { ?>


                <?php foreach ($attribute->getServiceAttributeOptions()->where(['!=', 'deleted', true])->all() as $serviceAttributeOption) { ?>
                    <div class="row" id="attribute-option-<?= $serviceAttributeOption->id ?>-parent">
                        <div class="col-md-9">
                            <div class="form-group">
                                <input type="text"
                                       name="<?= GlobalHelper::getModelName($model) ?>[attribute_options][<?= $serviceAttributeOption->id ?>]"
                                       value="<?= $serviceAttributeOption->name ?>" class="form-control"
                                       id="attribute-option-<?= $serviceAttributeOption->id ?>">
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <button type="button"
                                    data-target="#attribute-option-<?= $serviceAttributeOption->id ?>"
                                    class="btn btn-danger btn-sm remove-option">Remove
                            </button>
                        </div>
                    </div>
                <?php } ?>

            <?php } ?>


        </div>

    </div>

    <div class="row">
        <div class="col-md-6 text-right">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>


<?php ActiveForm::end() ?>


<?php

$js = <<<JS

function removeOption(element){
    var target = element.attr('data-target');
    var targetParent = target+'-parent';
    
    $(target).attr('value', '');
    $(targetParent).addClass('hidden');
}

$('.remove-option').click(function(e){
    e.preventDefault();
   removeOption($(this)); 
});

JS;

$this->registerJs($js);


?>