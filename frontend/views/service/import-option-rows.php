<?php

use yii\web\View;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;

/* @var View $this */
/** @var \common\models\ServiceAttribute $attribute */
/** @var \common\models\Service $service */
/** @var \common\forms\ImportOptionsFromExcel $model */

$this->title = 'Import Options';
$this->params['breadcrumbs'][] = ['label' => 'Services', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $service->name, 'url' => ['/service/view', 'id' => $service->id]];
$this->params['breadcrumbs'][] = ['label' => $attribute->name, 'url' => ['/service/edit-attribute', 'id' => $service->id, 'attribute_id' => $attribute->id]];
$this->params['breadcrumbs'][] = $this->title;

?>


<div class="row">
    <div class="col-md-12">


            <?php if (!empty($rows)) { ?>
                <div class="row">
                    <div class="col-md-12">

                        <b><u>Options to import</u></b>

                        <?php ActiveForm::begin([
                                'action' => ['service/import-options', 'attribute_id' => $attribute->id, 'service_id' => $service->id]
                        ]) ?>

                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Name Ar</th>
                                <th>Description</th>
                                <th>Description Ar</th>
                                <th>Mobile Description</th>
                                <th>Mobile Description Ar</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($rows as $index => $row) { ?>
                                <input type="hidden" name="rows[<?= $index ?>][name]" value="<?= isset($row['name']) ? $row['name'] : '' ?>">
                                <input type="hidden" name="rows[<?= $index ?>][name_ar]" value="<?= isset($row['name-ar']) ? $row['name-ar'] : '' ?>">
                                <input type="hidden" name="rows[<?= $index ?>][description]" value="<?= isset($row['description']) ? $row['description'] : '' ?>">
                                <input type="hidden" name="rows[<?= $index ?>][description_ar]" value="<?= isset($row['description-ar']) ? $row['description-ar'] : '' ?>">
                                <input type="hidden" name="rows[<?= $index ?>][mobile_description]" value="<?= isset($row['mobile-description']) ? $row['mobile-description'] : '' ?>">
                                <input type="hidden" name="rows[<?= $index ?>][mobile_description_ar]" value="<?= isset($row['mobile-description-ar']) ? $row['mobile-description-ar'] : '' ?>">
                                <tr>
                                    <td><?= isset($row['name']) ? $row['name'] : '' ?></td>
                                    <td><?= isset($row['name-ar']) ? $row['name-ar'] : '' ?></td>
                                    <td><?= isset($row['description']) ? $row['description'] : '' ?></td>
                                    <td><?= isset($row['description-ar']) ? $row['description-ar'] : '' ?></td>
                                    <td><?= isset($row['mobile-description']) ? $row['mobile-description'] : '' ?></td>
                                    <td><?= isset($row['mobile-description-ar']) ? $row['mobile-description-ar'] : '' ?></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>

                        <button class="btn btn-primary pull-right">Confirm Add</button>

                        <?php ActiveForm::end() ?>

                    </div>
                </div>
            <?php } ?>

    </div>
</div>

