<?php

use yii\helpers\Html;
use kartik\select2\Select2;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \common\models\Service */

$this->title = 'Set pricing attributes';
$this->params['breadcrumbs'][] = ['label' => 'Services', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['/service/view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

