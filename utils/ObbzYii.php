<?php
/**
 * @Author Mattakorn Limkool
 */

namespace obbz\yii2\utils;
use common\models\User;
use obbz\yii2\i18n\CoreFormatter;
use yii\base\Exception;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\FormatConverter;
use yii\helpers\Url;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * Class SC - Yii shortcut
 * @package Obbz\utils
 */
class ObbzYii
{
    const APP_FRONTEND_ID = 'app-frontend';
    const APP_BACKEND_ID = 'app-backend';
    const APP_CONSOLE_ID = 'app-console';
    const APP_API_ID = 'app-api';


    /**
     * @return string
     */
    public static function baseUrl($path = null){
        if($path){
            $path = "/" . $path;
        }
        return \Yii::$app->request->getBaseUrl() . $path;
    }

    public static function assetBaseUrl($path = null, $assetName = null){
        if(!isset($assetName)){
            if(\Yii::$app->id == self::APP_FRONTEND_ID){
                $assetName = 'frontend\assets\AppAsset';
            }else if(\Yii::$app->id == self::APP_BACKEND_ID){
                $assetName = 'backend\assets\AppAsset';
            }else if(\Yii::$app->id == self::APP_API_ID){
                $assetName = 'api\assets\AppAsset';
            }else{
                $assetName = '';
            }

            if($path){
                $path = "/" . $path;
            }
            return \Yii::$app->getAssetManager()->getBundle($assetName)->baseUrl . $path;
        }else{
            return '';
        }

    }

    public static function uploadUrl($path = ''){
        $uploadUrl = \Yii::getAlias('@uploadUrl');
        return $uploadUrl . '/' . $path;
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

    public static function queryParams(){
        return \Yii::$app->request->queryParams;
    }

    /**
     * @return CoreFormatter
     */
    public static function formatter(){
        return \Yii::$app->formatter;
    }

    public static function setHttpImage($file){
        $filename = basename($file);
        $file_extension = strtolower(substr(strrchr($filename,"."),1));

        switch( $file_extension ) {
            case "gif": $ctype="image/gif"; break;
            case "png": $ctype="image/png"; break;
            case "jpeg":
            case "jpg": $ctype="image/jpeg"; break;
            default:
                throw new Exception('Wrong Image file');
        }

        header('Content-type: ' . $ctype);
    }

    public static function setHttpHeaders($name, $mime, $encoding = 'utf-8')
    {
        \Yii::$app->response->format = Response::FORMAT_RAW;
        if (strstr($_SERVER["HTTP_USER_AGENT"], "MSIE") == false) {
            header("Cache-Control: no-cache");
            header("Pragma: no-cache");
        } else {
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Pragma: public");
        }
        header("Expires: Sat, 26 Jul 1979 05:00:00 GMT");
        header("Content-Encoding: {$encoding}");
        header("Content-Type: {$mime}; charset={$encoding}");
        header("Content-Disposition: attachment; filename={$name}");
        header("Cache-Control: max-age=0");
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
     * get/set cache
     * @return \yii\caching\Cache
     */
    public static function cache(){
        return \Yii::$app->cache;
    }

    /**
     * get current user and mapping to db
     * @return \common\models\User
     */
    public static function userDb(){
        return $userId = self::user()->identity;
//        return User::findIdentity($userId);
    }

    /**
     * get label from menu config
     * @param $menu
     * @param null $path - when not define this is current path
     * @param  $matchParams
     * @return string
     */
    public static function getTitleByMenu($menu, $path = null, $matchParams = true){
        $requestParams = [];

        if($matchParams){
            $requestParams = \Yii::$app->request->queryParams;

        }

        if(!isset($path)){
            $path = '/'.\Yii::$app->request->pathInfo;
        }
        foreach($menu as $item){
            if($matchParams){
                $requestUrlWithParams = $requestParams;
                $requestUrlWithParams[0] = $path;
                if($item['url'] == $requestUrlWithParams){
                    return $item['label'];
                }
            }else{
                if($item['url'][0] == $path){
                    return $item['label'];
                }
            }

        }
        return '';
    }


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

    /**
     *
     * @param $models Model[]
     * @param $condition array => ["field"=>"value"]  need only row that's field is equal a value
     *  todo - add more condition such as 'like', 'not equal'
     * @return mixed | Model[]
     */
    public static function modelsFilter($models, $condition){
        $result = [];
        foreach($models as $model){
            $allow = true;
            foreach($condition as $attribute => $value){
                if($model->$attribute != $value){
                    $allow = false;
                }
            }
            if($allow){
                $result[] = $model;
            }
        }
        return $result;
    }

    /**
     * @param $model Model
     * @return array
     */
    public static function performAjaxValidate($model){
        if (\Yii::$app->request->isAjax && $model->load(\Yii::$app->request->post())) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }else{
            return false;
        }
    }
    /**
     * @param $model Model
     * @return array
     */
    public static function checkAjaxValidate($model){
        if (\Yii::$app->request->isAjax && $model->load(\Yii::$app->request->post())) {
            return true;
        }else{
            return false;
        }
    }

    public static function debug($data, $end = true){
        echo "<pre>" . print_r($data, true) . "</pre>";
        if($end)
            exit;
    }


}