<?php
/**
 * Created by PhpStorm.
 * User: mattakorn
 * Date: 17/2/2560
 * Time: 16:30
 */

namespace obbz\yii2\widgets;

use yii\base\InvalidParamException;
use yii\base\Widget;
use yii\helpers\Html;


class Button extends Widget
{
    public $text;
    public $type = "submit";
    public $toggleText = "";
    public $btnClass = "default";
    public $options = array();
    public $prefixIcon = null;
    public $suffixIcon = null;

    public function init(){
        if(!isset($this->text)){
            throw new InvalidParamException('text is require');
        }
        parent::init();
    }

    public function run(){
        $prefixIcon = $this->prefixIcon ? Html::tag('i', '', ['class'=>'fa fa-' . $this->prefixIcon]) . " " : "";
        $suffixIcon = $this->suffixIcon  ? " ".Html::tag('i', '', ['class'=>'fa fa-' . $this->suffixIcon ]): "";

        $additionalOptions = [];
        if(!empty($toggleText)){
            $additionalOptions['data-toggle'] = "tooltip";
            $additionalOptions['data-original-title'] = $this->toggleText;
        }

        $options = array_merge([
            'class' => "btn btn-" . $this->btnClass,
            'type' => $this->type,

        ], $additionalOptions, $this->options);
        return Html::button($prefixIcon . $this->text . $suffixIcon ,$options);

    }

}