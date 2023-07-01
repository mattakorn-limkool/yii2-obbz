<?php
namespace obbz\yii2\extensions\fancybox5;
use yii\web\AssetBundle;

/**
 * @author  Mattakorn Limkool <obbz.dev@gmail.com>
 *
 */
class FancyboxAsset extends AssetBundle
{
    public $sourcePath = '@vendor/obbz/yii2/extensions/fancybox5/assets';
    public $css = [
        'fancybox/fancybox.css'
    ];
    public $js = [
        'fancybox/fancybox.umd.js'
    ];
}