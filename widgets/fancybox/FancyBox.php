<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\widgets\fancybox;


use yii\helpers\ArrayHelper;

class FancyBox extends \newerton\fancybox\FancyBox
{
    public $useDefaultIframConfig = false;
    public $defaultIframeConfig = [
        'type'=>'iframe',
        'fitToView'=> false,
        'maxWidth' => '800',
        'width' => '100%',
        'height' => '100%',
    ];

    public function init(){
        parent::init();
        $this->config = array_merge(
            $this->defaultIframeConfig,
            $this->config
        );
    }

    public function registerClientScript() {
        parent::registerClientScript();
        // custom assest
    }
}