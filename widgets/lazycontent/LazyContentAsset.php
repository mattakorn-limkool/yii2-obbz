<?php


namespace obbz\yii2\widgets\lazycontent;

class LazyContentAsset extends \yii\web\AssetBundle
{
    public $sourcePath = __DIR__;

    public $css = [
        'assets/style.css',
    ];

    public $js = [
        'assets/jquery.waypoints.min.js',
    ];


    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
