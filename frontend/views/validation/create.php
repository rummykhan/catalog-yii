<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Validation */

$this->title = 'Create Validation';
$this->params['breadcrumbs'][] = ['label' => 'Validations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="validation-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
