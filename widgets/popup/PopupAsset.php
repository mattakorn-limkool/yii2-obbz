<?php
namespace obbz\yii2\widgets\popup;
/**
 * @author: Mattakorn Limkool
 *
 */
class PopupAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@vendor/obbz/yii2/widgets/popup/assets';

    public $css = [
        'jquery.modal.min.css',
    ];

    public $js = [
        'jquery.modal.min.js'
    ];

    public $depends = [
        'yii\web\JqueryAsset'
    ];
}