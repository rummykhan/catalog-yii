<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\UserInputType */

$this->title = 'Create User Input Type';
$this->params['breadcrumbs'][] = ['label' => 'User Input Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-input-type-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
