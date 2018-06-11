<?php

use common\helpers\MultilingualInputHelper;
use common\models\Country;
use frontend\assets\LodashAsset;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Service */
/* @var $form yii\widgets\ActiveForm */

LodashAsset::register($this);
?>

    <div class="service-form">

        <?php $form = ActiveForm::begin(); ?>

        <div class="row">
            <div class="col-md-6">

                <?= MultilingualInputHelper::textInputs($form, $model, 'name') ?>

                <?= MultilingualInputHelper::textareaInputs($form, $model, 'description') ?>

                <?= MultilingualInputHelper::textareaInputs($form, $model, 'mobile_description') ?>

                <?= $form->field($model, 'active')->checkbox() ?>

            </div>

            <div class="col-md-6">

                <div class="form-group">
                    <label for="">Select category</label>
                    <?= Select2::widget([
                        'model' => $model,
                        'attribute' => 'category_id',
                        'data' => collect(\common\models\Category::find()->all())->pluck('name', 'id')->toArray(),
                        'value' => $model->category_id,
                        'options' => ['placeholder' => 'Select parent category']
                    ]) ?>
                </div>

                <div class="form-group">
                    <label for="">Select Country</label>
                    <?= Select2::widget(array(
                        'name' => 'country',
                        'data' => Country::toList(),
                        'value' => $model->getSelectedCountry(),
                        'options' => array('placeholder' => 'Select country'),
                        'id' => 'service-country'
                    )) ?>
                </div>

                <div class="form-group">
                    <label for="">Select cities</label>
                    <?= Select2::widget([
                        'name' => 'cities',
                        'data' => !empty($model->getSelectedCountry()) ? $model->getSelectedCountryCities() : [],
                        'value' => array_keys($model->getSelectedCities()),
                        'options' => ['placeholder' => 'Select city'],
                        'id' => 'service-cities',
                        'pluginOptions' => [
                            'multiple' => true,
                            'allowClear' => true
                        ]
                    ]) ?>
                </div>

                <div class="form-group">
                    <label for="">Select request types</label>
                    <?= Select2::widget([
                        'name' => 'request_types',
                        'data' => \common\models\RequestType::toList(),
                        'value' => $model->getActiveRequestTypesList(),
                        'options' => ['placeholder' => 'Select request type'],
                        'pluginOptions' => [
                            'multiple' => true,
                            'allowClear' => true
                        ]
                    ]) ?>
                </div>

                <?php if (!empty($model->image)) { ?>
                    <img src="<?= $model->getImageFileUrl('image') ?>" class="thumbnail" style="width: 150px;">
                <?php } ?>

                <?= $form->field($model, 'image')->fileInput() ?>

            </div>
        </div>


        <div class="row">
            <div class="col-md-12">

                <div class="form-group">
                    <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
                </div>
            </div>
        </div>


        <?php ActiveForm::end(); ?>

    </div>


<?php

$countriesMap = json_encode(Country::createMap());

$js = <<<JS

function resetCities(){
    $('#service-cities').html('');
}

function updateCities(countryId) {
    var countriesMap = JSON.parse('{$countriesMap}');
    
    var selectedCountry = _.find(countriesMap, {id: countryId});
    
    if(!selectedCountry){
        return false;
    }
    
    $.each(selectedCountry.cities, function(index, city){
        var newOption = new Option(city.name, city.id, false, false);
        $('#service-cities').append(newOption).trigger('change');
    });
   
}

$('#service-country').on('select2:select', function(e){
    
    var data = e.params.data;
    
    resetCities();
    
    if(!!data.id){
        updateCities(data.id);
    }
});

JS;

$this->registerJs($js);

?>