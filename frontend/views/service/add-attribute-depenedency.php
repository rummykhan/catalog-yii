<?php

use yii\web\View;
use kartik\select2\Select2;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model \common\models\Service */
/* @var $options array */
/* @var $attribute_id integer */
/* @var $depends_on_id integer */

$this->title = 'Add Attribute Dependency';
$this->params['breadcrumbs'][] = ['label' => 'Services', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['/service/view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;

?>

<?php ActiveForm::begin(['action' => ['/service/add-attribute-dependency', 'id' => $model->id], 'method' => 'GET']) ?>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Select Attribute</label>
                <?= Select2::widget([
                    'name' => 'attribute_id',
                    'data' => $model->getServiceAttributesList(),
                    'options' => ['placeholder' => 'Select attribute'],
                    'pluginOptions' => [
                        'multiple' => false,
                        'allowClear' => true
                    ]
                ]) ?>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="">Depends On</label>
                <?= Select2::widget([
                    'name' => 'depends_on_id',
                    'data' => $model->getServiceAttributesList(),
                    'options' => ['placeholder' => 'Select attribute'],
                    'pluginOptions' => [
                        'multiple' => false,
                        'allowClear' => true
                    ]
                ]) ?>
            </div>
            <div class="form-group">
                <button type="submit" id="get-options" class="btn btn-primary">Get Options</button>
            </div>
        </div>
    </div>

<?php ActiveForm::end() ?>


<?php if (!empty($attribute_id) && !empty($depends_on_id) && !empty($options)) { ?>

    <?php ActiveForm::begin([
        'action' => ['/service/attach-attribute-dependency', 'id' => $model->id],
        'method' => 'POST'
    ]) ?>

    <input type="hidden" name="attribute_id" value="<?= $attribute_id ?>">
    <input type="hidden" name="depends_on_id" value="<?= $depends_on_id ?>">

    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped">
                <tbody>
                <tr>
                    <td><?= \common\models\ServiceAttribute::findOne($attribute_id)->attribute0->name ?></td>
                    <th>Depends on</th>
                    <td><?= \common\models\ServiceAttribute::findOne($depends_on_id)->attribute0->name ?></td>
                    <th>When Option is</th>
                    <td>
                        <div class="form-group">
                            <?= Select2::widget([
                                'name' => 'service_attribute_option_id',
                                'data' => $options,
                                'options' => ['placeholder' => 'Select option'],
                                'pluginOptions' => [
                                    'multiple' => true,
                                    'allowClear' => true
                                ]
                            ]) ?>
                        </div>
                    </td>
                    <td>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <?php ActiveForm::end() ?>
<?php } ?>

    <hr>
<?php foreach ($model->serviceLevelAttributes as $serviceLevelAttribute) { ?>

    <?php if (!$serviceLevelAttribute->parental) {
        continue;
    } ?>

    <?php foreach ($serviceLevelAttribute->parental as $item) { ?>
        <ul class="list-group">
            <li class="list-group-item">
                <b><?= $item->serviceAttribute->attribute0->name ?></b> Depends on <b><?= $item->dependsOn->attribute0->name ?></b>
                When
                <b><?= $item->dependsOn->attribute0->name ?></b> is <b><?= $item->serviceAttributeOption->attributeOption->name ?></b>
            </li>
        </ul>
    <?php } ?>

<?php } ?>