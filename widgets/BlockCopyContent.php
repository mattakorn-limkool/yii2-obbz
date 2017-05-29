<?php
namespace obbz\yii2\widgets;

use backend\components\Roles;
use \Yii;
use yii\web\View;


class BlockCopyContent extends \yii\bootstrap\Widget
{
    public $selector = '.block-copy-content';
    public $ignoreBlockRoles = [Roles::THE_CREATOR];
    public $blockAlways = false;

    public function init(){
        parent::init();
        if($this->blockAlways){
            $this->registerBlockScript();
        }else{
            $doBlock = true;
            foreach($this->ignoreBlockRoles as $role){
                if(\Yii::$app->user->can($role)){
                    $doBlock = false;
                }
            }

            if($doBlock){
                $this->registerBlockScript();
            }
        }

    }

    public function registerBlockScript(){
        $view = $this->getView();
        $view->registerCss('
            '. $this->selector .' *{
                -webkit-touch-callout: none;
                -webkit-user-select: none;
                -khtml-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
                user-select: none;
            }
        ');
        $view->registerJs('

             ///  Block right click  ///
            var message="";function clickIE() {
                if (document.all) {
                    (message);return false;
                }
            }
            function clickNS(e) {
                if (document.layers||(document.getElementById&&!document.all)) {
                    if (e.which==2||e.which==3) {
                        (message);return false;}
                }
            }
            if (document.layers){
                document.captureEvents(Event.MOUSEDOWN);
                document.onmousedown=clickNS;
            }
            else{document.onmouseup=clickNS;document.oncontextmenu=clickIE;}
            document.oncontextmenu=new Function("return false");
        ', View::POS_HEAD);
        $view->registerJs('
            // Block f12
            $(document).keydown(function(event){
                if(event.keyCode==123){
                return false;
               }
                else if(event.ctrlKey && event.shiftKey && event.keyCode==73){
                  return false;  //Prevent from ctrl+shift+i
               }
            });
            ///  Block right click  ///
            $(document).on("contextmenu",function(e){
               e.preventDefault();
            });

            /// Block highlight text ///
            document.onselectstart=new Function ("return false");
            if (window.sidebar){
                document.onmousedown=_disableselect;
                document.onclick=_reEnable
            }

        ' ,View::POS_READY);
    }

}
