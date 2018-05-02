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
/* @var $service \common\models\Service */
/* @var $attribute \common\models\ServiceAttribute */

?>

<br>

<div class="row">
    <div class="col-md-6">
        <p>Options for <b>Service: <?= $service->name ?></b> & <b><?= $attribute->name ?></b></p>

        <ul class="list-group">
            <?php foreach ($attribute->getServiceAttributeOptions()->where(['deleted' => false])->all() as $option) { ?>
                <li class="list-group-item"><?= $option->name ?></li>
            <?php } ?>
        </ul>
    </div>
</div>


<br>

<p>Validations for <b>Service: <?= $service->name ?></b> & <b><?= $attribute->name ?></b></p>

<table class="table table-striped table-responsive">
    <thead>
    <tr>
        <th>Value</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($attribute->validations as $validation) { ?>
        <tr>
            <td><?= $validation->type ?></td>
            <td>
                <a href="<?= \yii\helpers\Url::to(['/attribute/detach-validation', 'validation_id' => $validation->id, 'service_attribute_id' => $attribute->id]) ?>"
                   class="btn btn-danger btn-sm">
                    Delete
                </a>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>

