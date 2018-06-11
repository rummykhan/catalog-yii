<?php

use common\helpers\MultilingualInputHelper;
use common\models\Category;
use common\models\Country;
use frontend\assets\LodashAsset;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model common\models\Category */
/* @var $form yii\widgets\ActiveForm */

LodashAsset::register($this);
?>

<div class="category-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">

            <?= MultilingualInputHelper::textInputs($form, $model, 'name') ?>

            <?= MultilingualInputHelper::textareaInputs($form, $model, 'description') ?>

            <div class="form-group">
                <label for="">Select Parent Category</label>
                <?= Select2::widget(array(
                    'model' => $model,
                    'attribute' => 'parent_id',
                    'data' => Category::toList(),
                    'value' => $model->parent_id,
                    'options' => array('placeholder' => 'Select parent category')
                )) ?>
            </div>

            <?= $form->field($model, 'active')->checkbox() ?>

        </div>

        <div class="col-md-6">

            <div class="form-group">
                <label for="">Select Country</label>
                <?= Select2::widget(array(
                    'name' => 'country',
                    'data' => Country::toList(),
                    'value' => $model->getSelectedCountry(),
                    'options' => array('placeholder' => 'Select country'),
                    'id' => 'category-country'
                )) ?>
            </div>

            <div class="form-group">
                <label for="">Select City</label>
                <?= Select2::widget(array(
                    'name' => 'cities',
                    'data' => !empty($model->getSelectedCountry()) ? $model->getSelectedCountryCities() : [],
                    'value' => array_keys($model->getSelectedCities()),
                    'options' => array('placeholder' => 'Select cities'),
                    'id' => 'category-cities',
                    'pluginOptions' => [
                        'multiple' => true
                    ]
                )) ?>
            </div>


            <?php if (!empty($model->image)) { ?>
                <img src="<?= $model->getImageFileUrl('image') ?>" alt="" width="150" class="thumbnail">
            <?php } ?>

            <?= $form->field($model, 'image')->fileInput() ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
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
    $('#category-cities').html('');
}

function updateCities(countryId) {
    var countriesMap = JSON.parse('{$countriesMap}');
    
    var selectedCountry = _.find(countriesMap, {id: countryId});
    
    if(!selectedCountry){
        return false;
    }
    
    $.each(selectedCountry.cities, function(index, city){
        var newOption = new Option(city.name, city.id, false, false);
        $('#category-cities').append(newOption).trigger('change');
    });
   
}

$('#category-country').on('select2:select', function(e){
    
    var data = e.params.data;
    
    resetCities();
    
    if(!!data.id){
        updateCities(data.id);
    }
});

JS;

$this->registerJs($js);

?>
