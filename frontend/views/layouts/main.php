<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);

    $menuItems = [
        ['label' => 'Home', 'url' => ['/site/index']],
        ['label' => 'Category', 'url' => ['/category/index']],
        ['label' => 'Service', 'url' => ['/service/index']],
        ['label' => 'Provider', 'url' => ['/provider/index']],
        [
            'label' => 'Attributes',
            'items' => [
                ['label' => 'Attribute', 'url' => ['/attribute/index']],
                ['label' => 'Option', 'url' => ['/attribute-option/index']],
            ]
        ],
        [
            'label' => 'Catalog Setting',
            'items' => [
                ['label' => 'Input Type', 'url' => ['/input-type/index']],
                ['label' => 'User Input Type', 'url' => ['/user-input-type/index']],
                ['label' => 'Validation', 'url' => ['/validation/index']],
                ['label' => 'Price Type', 'url' => ['/price-type/index']],
                ['label' => 'Country', 'url' => ['/country/index']],
                ['label' => 'City', 'url' => ['/city/index']],
                ['label' => 'Service Type', 'url' => ['/service-type/index']],
            ]
        ]
    ];

    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
    ]);
    NavBar::end(); ?>


    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; <?= Html::encode(Yii::$app->name) ?> <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
