<?php
/**
 * Created by PhpStorm.
 * User: rummykhan
 * Date: 6/4/18
 * Time: 1:30 PM
 */

use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;
use common\models\Service;

/** @var $model Service */

$this->title = 'Duplicate';
$this->params['breadcrumbs'][] = ['label' => 'Services', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['/service/view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Duplicate';

?>

<?php ActiveForm::begin() ?>

    <div class="row">
        <div class="col-md-6">

            <div class="form-group">
                <label for="">New Service Name</label>
                <input type="text" name="name" class="form-control">
            </div>

            <div class="form-group">
                <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
            </div>

        </div>
    </div>

<?php ActiveForm::end() ?>