<?php

use yii\web\View;
use yii\helpers\Url;

/** @var $this View */
/** @var $model \common\models\Provider */

?>

<p>
    <a href="<?= Url::to(['/provider/add-calendar', 'provider_id' => $model->id]) ?>" class="btn btn-primary">Add Calendar</a>
</p>
