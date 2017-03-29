<?php
/**
 * Created by PhpStorm.
 * User: mattakorn
 * Date: 17/2/2560
 * Time: 16:30
 */

namespace obbz\yii2\themes\material\widgets;

use yii\base\InvalidParamException;
use yii\bootstrap\Html;
use yii\bootstrap\Widget;
use yii\helpers\Url;


class CircleLink extends Widget
{
    public $url;
    public $icon;
    public $type = "primary";
    public $color; // color overwrite type
    public $toggleText = null;


    private $classColor = "";

    public function init(){
        if(!isset($this->icon)){
            throw new InvalidParamException('icon is require');
        }
        if(!isset($this->url)){
            throw new InvalidParamException('url is require');
        }

        parent::init();

        if(isset($color)){
            $this->classColor = "bgm-". $this->color;
        }else{
            $this->classColor = "btn-" . $this->type;
        }
    }

    public function run(){
        $btnClass = 'btn '. $this->classColor  .' btn-float waves-effect';
        $htmlIcon = '<i class="zmdi zmdi-'. $this->icon .'"></i>';

        $toggle = [];
        if(!empty($this->toggleText)){
            $toggle['data-toggle'] = 'tooltip';
            $toggle['data-original-title'] = $this->toggleText;
        }
        $this->options = array_merge([
            'class'=> $btnClass,
            'href' => Url::to($this->url),
        ],$toggle, $this->options);

        return  Html::tag('a', $htmlIcon ,$this->options);

    }

}