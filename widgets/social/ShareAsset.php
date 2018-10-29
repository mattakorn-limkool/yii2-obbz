<?php
namespace obbz\yii2\widgets\social;
/**
 * @author: Mattakorn Limkool
 *
 */
class ShareAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@vendor/obbz/yii2/widgets/social/assets';

    public $css = [
        'jssocials.css'
    ];
    public $js = [
        'jssocials.min.js'
    ];

    public $depends = [
        'yii\web\JqueryAsset'
    ];
}