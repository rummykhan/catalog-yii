<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\InputType */

$this->title = 'Create Attribute Input Type';
$this->params['breadcrumbs'][] = ['label' => 'Attribute Input Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="attribute-input-type-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
