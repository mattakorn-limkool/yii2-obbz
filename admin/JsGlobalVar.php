<?php
/**
 * @author  Mattakorn Limkool <obbz.dev@gmail.com>
 *
 */

namespace obbz\yii2\admin;


use obbz\yii2\utils\ObbzYii;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class JsGlobalVar extends Widget
{
    public $defaultCreateUrl = ['editor/flexible-module/create'];
    public function init()
    {
        parent::init();


        $createUrl = ArrayHelper::getValue(ObbzYii::app()->params, 'CKE_flexModuleIframeCreateUrl', Url::to($this->defaultCreateUrl, true));
        $scriptHead = '
                var CKE_flexModuleIframeCreateUrl = "'. $createUrl .'";
                var CKE_flexModuleIframeUrl = CKE_flexModuleIframeCreateUrl;
            ';

        $view = $this->getView();
        $view->registerJs($scriptHead, $view::POS_HEAD);
    }
}