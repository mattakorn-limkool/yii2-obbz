<?php
namespace obbz\yii2\widgets\popup;
/**
 * modal with jquery supported ajax like the fancybox.
 * @author: Mattakorn Limkool
 *
 *
 * @see https://github.com/kylefox/jquery-modal
 */
class Popup extends \yii\base\Widget
{
//    public $selector
    public $clientOptions;

    public function init(){
        PopupAsset::register($this->view);
//        $this->registerScript();
        parent::init();
    }

    protected function registerScript()
    {
        $clientOptions = empty($this->clientOptions) ? '' : Json::encode($this->clientOptions);
        $js = "jQuery('#{$this->options["id"]}').modal({$clientOptions});";
        $this->getView()->registerJs($js);
    }

}