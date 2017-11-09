<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace obbz\yii2\widgets\tagsinput;

use yii\web\AssetBundle;

/**
 * 
 * @author Mattakorn Limkool
 * @since 1.0
 */
class TagsInputAsset extends AssetBundle
{
    public $sourcePath = '@vendor/obbz/yii2/widgets/tagsinput/bootstrap-tags-input';

    public $js = [
    	'bootstrap-tagsinput.js'
    ];

    public $css = [
    	'bootstrap-tagsinput.css'
    ];
    
    public $depends = [
    	'yii\web\JqueryAsset'
    ];
}
