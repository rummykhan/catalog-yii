<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class JQueryUiAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'plugins/jquery-ui/jQuery-ui.css',
    ];
    public $js = [
        'plugins/jquery-ui/jQuery-ui.js'
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
