<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Attribute */
/* @var $service common\models\Service */

$this->title = 'Create Attribute';
if($service){
    $this->params['breadcrumbs'][] = ['label' => $service->name, 'url' => ['/service/view', 'id' => $service->id]];
}else{
    $this->params['breadcrumbs'][] = ['label' => 'Attributes', 'url' => ['index']];
}
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="attribute-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
