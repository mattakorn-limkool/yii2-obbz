<?php


namespace obbz\yii2\swagger;

use yii\web\AssetBundle;
use yii\web\View;

class CustomAsset extends AssetBundle
{
    public $sourcePath = '@vendor/obbz/yii2/swagger/assets/';

    public $js = [

    ];

    public $jsOptions = [
    ];

    public $css = [
        'screen.css',
    ];

    public $depends = [
        'obbz\yii2\swagger\SwaggerUIAsset',
    ];
}
