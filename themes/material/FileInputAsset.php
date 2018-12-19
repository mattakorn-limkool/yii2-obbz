<?php

namespace obbz\yii2\themes\material;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class FileInputAsset extends AssetBundle
{
    public $sourcePath = '@vendor/obbz/yii2/themes/material/assets';
    public $js = [
        'vendors/fileinput/fileinput.min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}
