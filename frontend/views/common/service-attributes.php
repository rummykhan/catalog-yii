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

<h4>Existing Attributes for <?= $model->name ?></h4>

<table class="table table-striped table-responsive">
    <thead>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Options</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    <?php /** @var \common\models\Attribute $attribute */
    foreach ($model->serviceAttributes as $attribute) { ?>
        <tr>
            <td><?= $attribute->id ?></td>
            <td><?= $attribute->name ?></td>
            <td>
                <?=
                \common\models\ServiceAttributeOption::find()
                    ->where([
                        'service_attribute_id' => \common\models\ServiceAttribute::find()->where(['service_id' => $model->id])->andWhere(['attribute_id' => $attribute->id])->one()->id
                    ])
                    ->count()
                ?>
            </td>
            <td>
                <a href="<?= \yii\helpers\Url::to(['/attribute/attach-options', 'attribute_id' => $attribute->id, 'service_id' => $model->id]) ?>"
                   class="btn btn-primary btn-sm">
                    View / Attach Options
                </a>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>


