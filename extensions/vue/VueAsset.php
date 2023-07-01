<?php
namespace obbz\yii2\extensions\vue;
use yii\web\AssetBundle;

/**
 * @author  Mattakorn Limkool <obbz.dev@gmail.com>
 *
 */
class VueAsset extends AssetBundle
{
    public $sourcePath = '@vendor/obbz/yii2/extensions/vue/assets';

    public function init()
    {
        $this->js = !YII_DEBUG ? [ 'vue.min.js'] : [ 'vue.js'];

        parent::init();
    }

}