<?php
/**
 * @Author Mattakorn Limkool
 */

namespace obbz\yii2\utils;

/**
 * Class SC - Yii shortcut
 * @package Obbz\utils
 */
class ObbzYii
{
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
     * @return mixed|\yii\web\User
     */
    public static function user(){
        return \Yii::$app->user;
    }
    #end Yii
}