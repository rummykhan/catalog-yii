<?php

use common\models\ServiceType;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \common\forms\AddCoverageArea */
/* @var $providedService common\models\ProvidedService */
/* @var $serviceType ServiceType */

$this->title = 'Add Coverage';
$this->params['breadcrumbs'][] = ['label' => $providedService->provider->username, 'url' => ['/provider/view', 'id' => $providedService->provider_id]];
$this->params['breadcrumbs'][] = ['label' => 'Provided Services', 'url' => ['/provided-service/index', 'provider_id' => $providedService->provider_id]];
$this->params['breadcrumbs'][] = ['label' => $providedService->service->name, 'url' => ['/provided-service/view', 'id' => $providedService->id]];
$this->params['breadcrumbs'][] = ['label' => $serviceType->type];
$this->params['breadcrumbs'][] = ['label' => 'Coverage Areas', 'url' => ['/provided-service/view-coverage-areas', 'id' => $providedService->id]];
$this->params['breadcrumbs'][] = $this->title;

$provider = $providedService->provider;
$service = $providedService->service;

$this->registerJsFile("https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&key=" . Yii::$app->params["googleMapsKey"]);
?>


<div class="provider-update">
    <?php $form = ActiveForm::begin(['action' => ['/provided-service/add-coverage-area', 'id' => $providedService->id, 'type' => $serviceType->id]]) ?>

    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'area_name')->textInput() ?>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="">Select City</label>
                <?= \kartik\select2\Select2::widget([
                    'model' => $model,
                    'attribute' => 'city_id',
                    'data' => \common\models\City::toList(),
                    'options' => ['placeholder' => 'Select City'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'multiple' => false,
                    ]
                ]) ?>
            </div>
        </div>
        <div class="col-md-6">
            <br>
            <div class="btn-group">
                <?php foreach ($providedService->getUnDeletedServiceTypes() as $providedServiceType) { ?>
                    <a href="<?= Url::to(['/provided-service/add-coverage-area', 'id' => $providedService->id, 'type' => $providedServiceType->serviceType->id]) ?>"
                       class="btn <?= $providedServiceType->serviceType->id == $serviceType->id ? 'btn-primary' : 'btn-default' ?>">
                        <?= $providedServiceType->serviceType->type ?>
                    </a>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="provider-form multi-area">
        <input id="pac-input" class="controls" type="text" placeholder="Search Box">
        <div class="map-container" style="position: relative">
            <div id="map-overlay" class="hidden">Validating Areas</div>
            <div id="map-canvas" style="width:100%; height:600px;">
            </div>
        </div>

        <?= $form->field($model, 'coverage_areas', ['template' => '{input}'])->hiddenInput() ?>
        <?= $form->field($model, 'service_type', ['template' => '{input}'])->hiddenInput() ?>
        <?= $form->field($model, 'provided_service_id', ['template' => '{input}'])->hiddenInput() ?>
        <?= $form->field($model, 'area_id', ['template' => '{input}'])->hiddenInput() ?>

        <br><br>
        <?= Html::submitButton('Update Areas', ['class' => 'btn btn-primary']) ?>


        <?php ActiveForm::end() ?>
        <br/>
    </div>
</div>


<?php

$this->registerJsFile("https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.9.0/underscore-min.js");

$coverageAreaId = Html::getInputId($model, 'coverage_areas');

?>

<script type="text/javascript">

    <?php
    $countries = [
        'AE' => '25.0750853,54.947555',
        'SA' => '24.7253981,46.2620208'
    ];

    $defaultCountry = 'AE';

    $first_area = !empty($coveredAreas) ? $coveredAreas[0] : null;
    $first_area = empty($first_area) ? (isset($countries[$defaultCountry]) ? $countries[$defaultCountry] : $countries[$defaultCountry]) : $first_area->coordinates;

    @list($lat, $long) = explode(',', $first_area);

    ?>

    <?php ob_start() ?>
    var coverageAreaId = '<?= $coverageAreaId ?>';
    var circles = {};
    var i = 1;
    var canAdd = true;
    var mapOptions = {
        zoom: 8,
        center: new google.maps.LatLng(<?=$lat?>, <?=$long?>),
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };

    console.log('mapOptions:', mapOptions);

    var map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);

    console.log('map:', map);

    // Create the search box and link it to the UI element.
    var input = document.getElementById('pac-input');
    var searchBox = new google.maps.places.Autocomplete(input, {componentRestrictions: {country: ['ae', 'sa']}});
    map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
    // Listen for the event fired when the user selects a prediction and retrieve
    // more details for that place.
    searchBox.addListener('place_changed', function () {
        console.log(searchBox);
        var place = searchBox.getPlace();
        // For each place, get the icon, name and location.
        var bounds = new google.maps.LatLngBounds();
        if (!place.geometry) {
            console.log("Returned place contains no geometry");
            return;
        }
        if (place.geometry.viewport) {
            // Only geocodes have viewport.
            bounds.union(place.geometry.viewport);
        } else {
            bounds.extend(place.geometry.location);
        }
        map.fitBounds(bounds);
        map.setZoom(13);
    });

    google.maps.event.addListener(map, "click", function (event) {
        if (!canAdd)
            return;
        canAdd = false;
        var circle1 = {
            id: i++,
            strokeColor: "#0000FF",
            strokeOpacity: 0.8,
            strokeWeight: 1,
            fillColor: "#0000FF",
            fillOpacity: 0.2,
            map: map,
            center: event.latLng,
            draggable: true,
            editable: true,
            radius: 10000,
            valid: false
        };
        var mapcircle = new google.maps.Circle(circle1);
        circles[mapcircle.id] = mapcircle;
        validateCircleLocation(mapcircle);
        google.maps.event.addListener(mapcircle, 'rightclick', function () {
            if (confirm("Remove this area?")) {
                mapcircle.setMap(null);
                delete circles[mapcircle.id];
                updateInputs();
            }
        });
        google.maps.event.addListener(mapcircle, 'center_changed', function () {
            validateCircleLocation(mapcircle);
        });
        google.maps.event.addListener(mapcircle, 'radius_changed', function () {
            console.log(parseInt(mapcircle.getRadius()));
            updateInputs();
        });
    });

    var centerChangedTimeout = null;
    function validateCircleLocation(mapcircle) {
        clearTimeout(centerChangedTimeout);
        centerChangedTimeout = setTimeout(function () {
            blockMap();
            //console.log(mapcircle.getCenter().lat(), mapcircle.getCenter().lng());
            new google.maps.Geocoder().geocode({
                latLng: mapcircle.getCenter()
            }, function (results, status) {
                if (status == google.maps.GeocoderStatus.OK && results && results.length) {
                    var findResult = function (results, name) {
                        var result = _.find(results, function (obj) {
                            return obj.types[0] == name && obj.types[1] == "political";
                        });
                        return result ? result.short_name : null;
                    };

                    var country = findResult(results[0].address_components, "country");
                    //if (country.toLowerCase() == 'ae' || country.toLowerCase() == 'sa') {
                    if (country.toLowerCase() == '<?=strtolower($defaultCountry)?>') {
                        //console.log(results[0].geometry.location);
                        //console.log(mapcircle);
                        mapcircle.setOptions({
                            strokeColor: '#0000FF',
                            fillColor: '#0000FF',
                            valid: true
                        });
                    } else {
                        mapcircle.setOptions({
                            strokeColor: '#FF0000',
                            fillColor: '#FF0000',
                            valid: false
                        });
                    }
                } else {
                    mapcircle.setOptions({
                        strokeColor: '#FF0000',
                        fillColor: '#FF0000',
                        valid: false
                    });
                }
                unblockMap();
            });
        }, 500);
    }
    function blockMap() {
        $("#map-overlay").removeClass("hidden");
        canAdd = false;
    }
    function unblockMap() {
        $("#map-overlay").addClass("hidden");
        canAdd = true;
        updateInputs();
    }
    function updateInputs() {
        $("#" + coverageAreaId).val('');
        var val = [];
        $.each(circles, function (index, circle) {
            if (circle.valid) {
                val.push({
                    coordinates: circle.getCenter().lat() + "," + circle.getCenter().lng(),
                    radius: Math.floor(circle.getRadius())
                });
            }
        });
        $("#" + coverageAreaId).val(JSON.stringify(val));
    }
    <?php foreach ($coveredAreas as $key => $coveredArea) { ?>
    var circle<?= $coveredArea->id ?> = {
        id: i++,
        strokeColor: "#0000FF",
        strokeOpacity: 0.8,
        strokeWeight: 1,
        fillColor: "#0000FF",
        fillOpacity: 0.2,
        map: map,
        center: new google.maps.LatLng(<?= $coveredArea->coordinates ?>),
        draggable: true,
        editable: true,
        radius: <?= $coveredArea->radius ?>,
        valid: true
    };
    var mapcircle<?= $coveredArea->id ?> = new google.maps.Circle(circle<?= $coveredArea->id ?>);
    circles[mapcircle<?= $coveredArea->id ?>.id] = mapcircle<?= $coveredArea->id ?>;
    google.maps.event.addListener(mapcircle<?= $coveredArea->id ?>, 'rightclick', function () {
        if (confirm("Remove this area?")) {
            mapcircle<?= $coveredArea->id ?>.setMap(null);
            delete circles[mapcircle<?= $coveredArea->id ?>.id];
            updateInputs();
        }
    });
    google.maps.event.addListener(mapcircle<?= $coveredArea->id ?>, 'center_changed', function () {
        validateCircleLocation(mapcircle<?= $coveredArea->id ?>);
    });
    google.maps.event.addListener(mapcircle<?= $coveredArea->id ?>, 'radius_changed', function () {
        updateInputs();
    });
    <?php } ?>
    updateInputs();
    <?php $js = ob_get_clean(); ?>
</script>
<?php $this->registerJs($js) ?>
<style type="text/css">
    .multi-area #map-overlay.hidden {
        display: none;
    }

    .multi-area #map-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 100%;
        bottom: 0;
        width: 100%;
        z-index: 999;
        background: rgba(255, 255, 255, 0.5);
        display: -webkit-flex;
        display: flex;
        -webkit-justify-content: center;
        justify-content: center;
        -webkit-align-items: center;
        align-items: center;
        text-shadow: 1px 1px #ccc;
        font-weight: bold;
        font-size: 31px;
        color: #5c5c5c;
    }

    .multi-area #pac-input {
        background-color: #fff;
        font-family: Roboto;
        font-size: 15px;
        font-weight: 300;
        margin-left: 12px;
        padding: 0 11px 0 13px;
        text-overflow: ellipsis;
        width: 300px;
    }

    .multi-area .controls {
        margin-top: 10px;
        border: 1px solid transparent;
        border-radius: 2px 0 0 2px;
        box-sizing: border-box;
        -moz-box-sizing: border-box;
        height: 32px;
        outline: none;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
    }
</style>