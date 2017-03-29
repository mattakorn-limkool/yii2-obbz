<?php
/**
 * Created by PhpStorm.
 * User: mattakorn
 * Date: 17/2/2560
 * Time: 16:30
 */

namespace obbz\yii2\widgets;

use yii\base\InvalidParamException;

use yii\helpers\Url;

class ButtonLink extends Button
{

    public $url;
    public $type = "button";
    public $toggleText = "";

    public function init(){
        if(!isset($this->url)){
            throw new InvalidParamException('url is require');
        }
        parent::init();
    }

    public function run(){
        $url = Url::to($this->url);
        if(isset($this->options['target']) && $this->options['target'] === '_blank'){
            $linkJs = 'window.open(\''. $url .'\')';
        }else{
            $linkJs = 'location.href =\''. $url .'\'';
        }

        $toggle = [];
        if(!empty($this->toggleText)){
            $toggle['data-toggle'] = 'tooltip';
            $toggle['data-original-title'] = $this->toggleText;
        }

        $this->options = array_merge([
            'onclick'=>  $linkJs
        ], $toggle, $this->options);

        return parent::run();

    }

}