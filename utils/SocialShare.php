<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\utils;

use yii\base\Component;
use yii\base\ErrorException;

class SocialShare extends Component
{
    public $link;
    public $title;
    public $facebookAppId;

    public function init(){
        if(empty($this->link)){
            $this->link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        }
        if(empty($this->facebookAppId)){
            if(\Yii::$app->params['facebook.appId']){
                $this->facebookAppId = \Yii::$app->params['facebook.appId'];
            }else{
                throw new ErrorException('Please set facebook app id');
            }

        }
        parent::init();
    }


    public function facebookLink(){
        return 'https://www.facebook.com/sharer/sharer.php?u=' . $this->link;
    }

    public function lineLink(){
        return 'https://timeline.line.me/social-plugin/share?url='.$this->link;
    }

    public function twitterLink(){
        return 'https://twitter.com/share';
    }
}