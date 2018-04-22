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
/* @var $attribute \common\models\Attribute */


$serviceAttribute = \common\models\ServiceAttribute::find()
    ->where(['service_id' => $service->id])
    ->andWhere(['attribute_id' => $attribute->id])
    ->one();

?>

<br>

<p>Options for <b><?= $service->name ?></b> attribute <b><?= $attribute->name ?></b></p>

<table class="table table-striped table-responsive">
    <thead>
    <tr>
        <th>Value</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach($serviceAttribute->serviceAttributeOptions as $option) { ?>
        <tr>
            <td><?= $option->attributeOption->name ?></td>
            <td>
                <a href="<?= \yii\helpers\Url::to(['/attribute/detach-options', 'option_id' => $option->id, 'service_attribute_id' => $option->service_attribute_id ]) ?>" class="btn btn-danger btn-sm">
                    Delete
                </a>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>


