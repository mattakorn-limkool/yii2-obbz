<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\utils;

use ijackua\sharelinks\ShareLinks;
use yii\base\Component;
use yii\base\ErrorException;

/**
 * utilities for share url with social plugin without register app on social media
 * Class SocialShare
 * @package obbz\yii2\utils
 */
class SocialShare extends ShareLinks
{
    const SOCIAL_LINE = 8;


    public function init(){
        parent::init();
        $this->shareUrlMap[self::SOCIAL_LINE] = 'https://timeline.line.me/social-plugin/share?url={url}';
    }

    public function twitterLink($url = null){
        return self::shareUrl(self::SOCIAL_TWITTER, $url);
    }

    public function facebookLink($url = null){
        return self::shareUrl(self::SOCIAL_FACEBOOK, $url);
    }

    public function lineLink($url = null){
        return self::shareUrl(self::SOCIAL_LINE, $url);
    }

    public function googlePlusLink($url = null){
        return self::shareUrl(self::SOCIAL_GPLUS, $url);
    }

    public function vkontakteLink($url = null){
        return self::shareUrl(self::SOCIAL_VKONTAKTE, $url);
    }



    public function linkedinLink($url = null){
        return self::shareUrl(self::SOCIAL_LINKEDIN, $url);
    }

    public function kindleLink($url = null){
        return self::shareUrl(self::SOCIAL_KINDLE, $url);
    }

    public function kindleXing($url = null){
        return self::shareUrl(self::SOCIAL_XING, $url);
    }

    public static function registerMetaTags($seoParams){
        foreach($seoParams as $key=>$param){
            if($param != null){
                \Yii::$app->view->registerMetaTag(['name'=>$key, 'content'=>$param], $key);
            }
        }
    }


}