<?php
/**
 * Created by PhpStorm.
 * User: rummykhan
 * Date: 5/1/18
 * Time: 3:27 PM
 */
use yii\widgets\DetailView;

/* @var $this \yii\web\View */
/* @var $service \common\models\Service  */

?>

<?= DetailView::widget([
    'model' => $service,
    'attributes' => [
        'id',
        'name',
        [
            'label' => 'category',
            'attribute' => 'parent_id',
            'value' => function ($service) {
                if (!$service->category) {
                    return null;
                }

                return $service->category->name;
            }
        ],
        [
            'label' => 'Cities',
            'value' => function ($service) {
                /** @var $service \common\models\Service */
                return implode(',', collect($service->getCities()->asArray()->all())->pluck('name')->toArray());
            }
        ],
        'created_at',
        'updated_at',
    ],
]) ?>
