<?php
/**
 * Created by PhpStorm.
 * User: rummykhan
 * Date: 4/22/18
 * Time: 2:15 PM
 */

use yii\web\View;
use common\models\Service;

/* @var $this View */
/* @var $model \common\models\Service */

?>

<br>

<h4>Existing Fields for <?= $model->name ?></h4>

<table class="table table-striped table-responsive">
    <thead>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Price Type</th>
        <th>No of Options</th>
        <th>Validations</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    <?php /** @var \common\models\ServiceAttribute $attribute */
    foreach ($model->getServiceAttributes()->where(['deleted' => false])->all() as $attribute) { ?>
        <tr>
            <td><?= $attribute->id ?></td>
            <td><?= $attribute->name ?></td>
            <td><?= ucwords($attribute->getPriceType()) ?></td>
            <td><?= $attribute->getServiceAttributeOptions()->where(['deleted' => false])->count() ?></td>
            <td><?= $attribute->validationsString ?></td>
            <td>
                <a href="<?= \yii\helpers\Url::to(['/service/edit-attribute', 'id' => $model->id, 'attribute_id' => $attribute->id]) ?>"
                   class="btn btn-primary btn-sm">
                    Edit Field
                </a>
                <a href="<?= \yii\helpers\Url::to(['/service/delete-attribute', 'attribute_id' => $attribute->id, 'service_id' => $model->id]) ?>"
                   class="btn btn-primary btn-sm">
                    Delete Field
                </a>
                <a href="<?= \yii\helpers\Url::to(['/service/import-excel', 'attribute_id' => $attribute->id, 'service_id' => $model->id]) ?>"
                   class="btn btn-primary btn-sm">
                    Import Options from Excel
                </a>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>


