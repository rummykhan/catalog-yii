<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ServiceAttributeDepends */

$this->title = 'Create Service Attribute Depends';
$this->params['breadcrumbs'][] = ['label' => 'Service Attribute Depends', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="service-attribute-depends-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
