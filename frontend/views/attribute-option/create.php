<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model common\models\AttributeOption */
/* @var $returnTo string */

$this->title = 'Create Attribute Option ';
$this->params['breadcrumbs'][] = ['label' => 'Attribute Options', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="attribute-option-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin([
        'action' => ['/attribute-option/create', 'returnTo' => $returnTo]
    ]); ?>

    <?= $this->render('_form', [
        'model' => $model,
        'form' => $form
    ]) ?>

    <?php ActiveForm::end(); ?>

</div>
