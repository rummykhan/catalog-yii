<?php
/**
 * Created by PhpStorm.
 * User: rummykhan
 * Date: 5/1/18
 * Time: 3:27 PM
 */
use yii\widgets\DetailView;

/* @var $this \yii\web\View */
/* @var $service \common\models\Service */

?>

<?= DetailView::widget([
    'model' => $service,
    'attributes' => [
        'id',
        'name',
        'slug',
        'description',
        [
            'label' => 'image',
            'value' => function ($model) {

                if (empty($model->image)) {
                    return null;
                }

                /** @var $model \common\models\Service */
                return \yii\helpers\Html::img($model->getImageFileUrl('image'), ['class' => 'thumbnail', 'width' => '100']);
            },
            'format' => 'html'
        ],
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
        [
            'label' => 'Mobile UI Type',
            'value' => $service->mobile_ui_style_label,
        ]
    ],
]) ?>
