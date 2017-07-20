<?php

namespace obbz\yii2\extensions\ckeditor;

use yii\web\AssetBundle;

class CKEditorWidgetAsset extends AssetBundle
{
    public $sourcePath = '@vendor/obbz/yii2/extensions/ckeditor/assets/';

    public $depends = [
        'dosamigos\ckeditor\CKEditorAsset'
    ];

    public $js = [
        'ckeditor.widget.js'
    ];
}
