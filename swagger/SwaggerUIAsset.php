<?php


namespace obbz\yii2\swagger;

use yii\web\AssetBundle;
use yii\web\View;

class SwaggerUIAsset extends AssetBundle
{
    public $sourcePath = '@bower/swagger-ui/dist';

    public $js = [
        'lib/jquery-1.8.0.min.js',
        'lib/jquery.slideto.min.js',
        'lib/jsoneditor.min.js',
        'lib/jquery.wiggle.min.js',
        'lib/jquery.ba-bbq.min.js',
        'lib/handlebars-2.0.0.js',
        'lib/underscore-min.js',
        'lib/backbone-min.js',
        'swagger-ui.js',
        'lib/highlight.7.3.pack.js',
        'lib/marked.js',
        'lib/swagger-oauth.js',
    ];

    public $jsOptions = [
        'position' => View::POS_HEAD,
    ];

    public $css = [
        'css/typography.css',
        'css/reset.css',
        'css/screen.css',
    ];


}
