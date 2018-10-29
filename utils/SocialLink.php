<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\utils;


use yii\base\ErrorException;

class SocialLink
{
    /**  LINE */
    public static function line($name){
        return 'https://line.me/ti/p/' . urlencode($name);
    }
    public static function appLine(){
        if(isset(ObbzYii::app()->params['socialLink.line'])){
            return self::line(ObbzYii::app()->params['socialLink.line']);
        }else{
            throw new ErrorException("Please setup socialLink.line to params");
        }
    }

    /** FACEBOOK */
    public static function fb($name){
        return 'https://www.facebook.com/' . $name;
    }
    public static function appFb(){
        if(isset(ObbzYii::app()->params['socialLink.fb'])){
            return self::fb(ObbzYii::app()->params['socialLink.fb']);
        }else{
            throw new ErrorException("Please setup socialLink.fb to params");
        }
    }

    /** YOUTUBE */
    public static function youtube($name){
        return 'https://www.youtube.com/channel/' . $name;
    }
    public static function appYoutube(){
        if(isset(ObbzYii::app()->params['socialLink.youtube'])){
            return self::youtube(ObbzYii::app()->params['socialLink.youtube']);
        }else{
            throw new ErrorException("Please setup socialLink.youtube to params");
        }
    }




}