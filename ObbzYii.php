<?php
/**
 * @Author Mattakorn Limkool
 */

namespace Obbz\utils;

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
}