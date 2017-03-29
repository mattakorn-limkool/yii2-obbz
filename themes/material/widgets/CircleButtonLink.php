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

class CircleButtonLink extends ButtonLink
{
    public $icon;
    public $type = "primary";
    public $color; // color overwrite type
    private $classColor = "";
    public $toggleText = null;

    public function init(){
        $htmlIcon = '<i class="zmdi zmdi-'. $this->icon .'"></i>';
        $this->text = $htmlIcon;

        parent::init();

        if(isset($color)){
            $this->classColor = "bgm-". $this->color;
        }else{
            $this->classColor = "btn-" . $this->type;
        }
    }

    public function run(){
        $btnClass = 'btn '. $this->classColor  .' btn-float waves-effect';

        $this->options = array_merge([
            'class'=> $btnClass,
        ], $this->options);

        return parent::run();


    }

}