<?php
/**
 * @Author Mattakorn Limkool
 */

namespace obbz\yii2\utils;
use common\models\User;
use yii\helpers\FormatConverter;
use yii\helpers\Url;

/**
 * Class SC - Yii shortcut
 * @package Obbz\utils
 */
class ObbzYii
{
    const DB_DATETIME_FORMAT = "php:Y-m-d H:i:s";
    const DB_DATE_FORMAT = "php:Y-m-d";
    const DB_TIME_FORMAT = "php:H:i:s";

    /**
     * @return string
     */
    public static function baseUrl($path = null){
        if($path){
            $path = "/" . $path;
        }
        return \Yii::$app->request->getBaseUrl() . $path;
    }

    public static function referrerUrl($defaultUrl = null){
        if(!isset(\Yii::$app->request->referrer)){
            if(self::isExternalUrl($defaultUrl)){
                return $defaultUrl;
            }else{
                return Url::to($defaultUrl);
            }

        }
        return \Yii::$app->request->referrer;
    }

    /**
     * @return \yii\console\Request|\yii\web\Request
     */
    public static function request(){
        return \Yii::$app->request;
    }
    #region REQUEST
    /**
     * @param null $name
     * @param null $defaultValue
     * @return array|mixed
     */
    public static function post($name = null, $defaultValue = null){
        return \Yii::$app->request->post($name, $defaultValue);
    }

    /**
     * @param null $name
     * @param null $defaultValue
     * @return array|mixed
     */
    public static function get($name = null, $defaultValue = null){
        return \Yii::$app->request->get($name, $defaultValue);
    }

    #endregion

    #Yii
    /**
     * set flash message
     * @param $key
     * @param bool|true $value
     * @param bool|true $removeAfterAccess
     */
    public static function setFlash($key, $value = true, $removeAfterAccess = true){
        \Yii::$app->session->setFlash($key, $value, $removeAfterAccess);
    }

    public static function setFlashSuccess($value = true, $removeAfterAccess = true){
        self::setFlash('success', $value, $removeAfterAccess);
    }
    public static function setFlashError($value = true, $removeAfterAccess = true){
        self::setFlash('error', $value, $removeAfterAccess);
    }
    public static function setFlashInfo($value = true, $removeAfterAccess = true){
        self::setFlash('info', $value, $removeAfterAccess);
    }
    public static function setFlashWarning($value = true, $removeAfterAccess = true){
        self::setFlash('warning', $value, $removeAfterAccess);
    }

    /**
     * get current user
     * @return mixed|\yii\web\User
     */
    public static function user(){
        return \Yii::$app->user;
    }

    /**
     * get current user and mapping to db
     * @return \common\models\User
     */
    public static function userDb(){
        $userId = self::user()->id;
        return User::findIdentity($userId);
    }

    /**
     * Translates a message to the specified language.
     * @param $message
     * @param string $category
     * @param array $params
     * @param null $language
     * @return string
     */
    public static function t($message, $category = "app", $params = [], $language = null){
        return \Yii::t($category, $message, $params, $language);
    }
    #end Yii


    #region utils

    /**
     * get current/custom date database format
     * @param null $dateStr
     * @param string $type
     * @param null $format
     * @return bool|string
     */
    public static function dateDb($dateStr = null, $type='date', $format = null){
        if ($type === 'datetime') {
            $fmt = ($format == null) ? self::DB_DATETIME_FORMAT : $format;
        }
        elseif ($type === 'time') {
            $fmt = ($format == null) ? self::DB_TIME_FORMAT : $format;
        }
        else {
            $fmt = ($format == null) ? self::DB_DATE_FORMAT : $format;
        }

        if($dateStr === null){
            if (strncmp($fmt, 'php:', 4) === 0) {
                $fmt = substr($fmt, 4);
            }
            return date($fmt);
        }else{
            return \Yii::$app->formatter->asDate($dateStr, $fmt);
        }

    }



    /**
     * default date format  d-m-Y H:i:s
     * @param null $date
     * @param bool $showTime
     * @param string $dateFormat
     * @param string $timeFormat
     * @return bool|string
     */
//    function dateFormat($date = null, $showTime=false, $dateFormat='d-m-Y', $timeFormat='H:i:s'){
//        if($date === null){
//            $time = time();
//        }
//
//        $time = strtotime($date);
//        if($showTime)
//            return date($dateFormat. ' ' . $timeFormat, $time);
//        else
//            return date($dateFormat, $time);
//
//    }
    #endregion
    /**
     * Retrieves the value of an array element or object property with the given key or property name.
     * @param $object array|object
     * @param $value - key of array|object
     * @param null $default
     * @return null
     */
    public static function getValue($object, $value, $default = null){
        if(!empty($object[$value])){
            return $object[$value];
        }else{
            return $default;
        }
    }

    public static function isExternalUrl($url) {
        if(is_string($url)){
            $components = parse_url($url);
            return !empty($components['host']) && strcasecmp($components['host'], 'example.com'); // empty host will indicate url like '/relative.php'

        }else{
            return false;
        }
    }

    public static function debug($data){
        echo "<pre>" . print_r($data, true) . "</pre>";
        exit;
    }
}