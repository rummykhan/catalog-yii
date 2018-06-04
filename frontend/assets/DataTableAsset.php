<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class DataTableAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'plugins/data-table/data-table.min.css',
    ];
    public $js = [
        'plugins/data-table/data-table.min.js'
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
