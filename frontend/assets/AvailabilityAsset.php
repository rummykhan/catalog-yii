<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AvailabilityAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
    ];

    public $js = [
        'plugins/availability/availability.js',
        'plugins/availability/provider-calendar.js',
    ];

    public $depends = [
        'frontend\assets\FullCalendarAsset'
    ];
}
