<?php
/**
 * Created by PhpStorm.
 * User: mattakorn
 * Date: 17/2/2560
 * Time: 16:30
 */

namespace obbz\yii2\themes\material\widgets;

use obbz\yii2\widgets\ButtonLink;
use yii\base\Widget;

class CircleButtonLink extends Widget
{
    public $icon;
    public $url;
    public $toggleText = "";
    public $type = "primary";
    public $color; // color overwrite type
    public $options = array();

    private $classColor = "";

    public function init(){
        parent::init();

        if(isset($color)){
            $this->classColor = "bgm-". $this->color;
        }else{
            $this->classColor = "btn-" . $this->type;
        }
    }

    public function run(){
        $htmlIcon = '<i class="zmdi zmdi-'. $this->icon .'"></i>';
        $btnClass = 'btn '. $this->classColor  .' btn-float waves-effect';

        return ButtonLink::widget([
            'text'=>$htmlIcon,
            'url'=> $this->url,
            'toggleText'=> $this->toggleText,
            'btnClass'=> $btnClass,
            'options'=> $this->options,
        ]);
    }

}