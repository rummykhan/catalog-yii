<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\AttributeType */

$this->title = 'Create Attribute Type';
$this->params['breadcrumbs'][] = ['label' => 'Attribute Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="attribute-type-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
