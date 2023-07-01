<?php
/**
 * @Author Mattakorn Limkool
 */

namespace obbz\yii2\utils;
use alexandernst\devicedetect\DeviceDetect;
use common\models\User;
use obbz\yii2\i18n\CoreFormatter;
use yii\base\Exception;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\FormatConverter;
use yii\helpers\Url;
use yii\mail\MessageInterface;
use yii\web\Response;
use yii\web\View;
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
     * @return \yii\console\Application|\yii\web\Application
     */
    public static function app(){
        return \Yii::$app;
    }

    public static function appId(){
        return \Yii::$app->id;
    }

    /**
     * @return string
     */
    public static function baseUrl($path = null, $scheme = false){
        if($path){
            $path = "/" . $path;
        }
        return Url::base($scheme) . $path;
    }

    /**
     *  return base asset url for default asset
     * @param null $path
     * @param null $assetName
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
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
        }
//        if(\Yii::$app->id == self::APP_FRONTEND_ID){
//            $assetName = 'frontend\assets\AppAsset';
//        }else if(\Yii::$app->id == self::APP_BACKEND_ID){
//            $assetName = 'backend\assets\AppAsset';
//        }else if(\Yii::$app->id == self::APP_API_ID){
//            $assetName = 'api\assets\AppAsset';
//        }else if(isset($assetName)){
//            $assetName = $assetName;
//        }else{ // null assetName
//            $assetName = '';
//        }
//        ObbzYii::debug($assetName);
        if($path){
            $path = "/" . $path;
        }
        return \Yii::$app->getAssetManager()->getBundle($assetName)->baseUrl . $path;

    }

    public static function isFrontend(){
        return \Yii::$app->id == self::APP_FRONTEND_ID ? true : false;
    }
    public static function isBackend(){
        return \Yii::$app->id == self::APP_BACKEND_ID ? true : false;
    }
    public static function isApi(){
        return \Yii::$app->id == self::APP_API_ID ? true : false;
    }
    public static function isConsole(){
        return \Yii::$app->id == self::APP_CONSOLE_ID ? true : false;
    }



    /**
     * default upload url for app
     * @param string $path
     * @return string
     */
    public static function uploadUrl($path = ''){
        $uploadUrl = \Yii::getAlias('@uploadUrl');
        return $uploadUrl . '/' . $path;
    }

    /**
     * referrerUrl with default url
     * @param null $defaultUrl
     * @return mixed|null|string
     */
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
     * get returnUrl with default url
     * @param null $defaultUrl
     * @return mixed|null|string
     */
    public static function getReturnUrl($defaultUrl = null){
        if(!isset(self::user()->returnUrl)){
            if(self::isExternalUrl($defaultUrl)){
                return $defaultUrl;
            }else{
                return Url::to($defaultUrl);
            }

        }else{
            return self::user()->returnUrl;
        }

    }

    /**
     * set return url default by current url
     * @param null $url
     */
    public static function setReturnUrl($url = null){
        if(!isset($url)){
            $url = Url::current();
        }
        self::user()->returnUrl = $url;
    }



    /**
     * generate url by form params
     * @param $url - default url array of yii
     * @param Model $form
     * @param array $params
     * @return string
     */
    public static function formParamsUrl($url, $form, $params = []){
        $urlString = Url::to($url);
        if(is_string($form)){
            $className = \yii\helpers\StringHelper::basename($form);
        }else{
            $className = \yii\helpers\StringHelper::basename(get_class($form));
        }
        $tail = '';

        if(!empty($params)){
            $tail = '?';
            if (strpos($urlString, '?') !== false) {
                $tail = '&';
            }

            foreach($params as $key => $value){
                $tail .= $className . '[' . $key . ']=' . $value . '&';
            }
            $tail= rtrim($tail,"&");

        }


        return $urlString.$tail;
    }


    #region REQUEST

    /**
     * @return \yii\console\Request|\yii\web\Request
     */
    public static function request(){
        return \Yii::$app->request;
    }

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

    #endregion

    /**
     * @return CoreFormatter
     */
    public static function formatter(){
        return \Yii::$app->formatter;
    }


    public static function getSession(){
        return \Yii::$app->session;
    }

    #region cookies
    /**
     * @param $name
     * @param null $defaultValue
     * @return mixed
     */
    public static function getCookie($name, $defaultValue = null){
        $cookies = ObbzYii::app()->request->cookies;
        return $cookies->getValue($name, $defaultValue);
    }

    /**
     * @param $name
     * @return bool
     */
    public static function hasCookie($name){
        $cookies = ObbzYii::app()->request->cookies;
        return $cookies->has($name);
    }

    /**
     * @param \yii\web\Cookie $cookie
     */
    public static function setCookie(\yii\web\Cookie $cookie){
        $cookies = ObbzYii::app()->response->cookies;
        $cookies->add($cookie);
    }

    /**
     * @param $name
     */
    public static function removeCookie($name){
        $cookies = ObbzYii::app()->response->cookies;
        $cookies->remove($name);
    }

    #endregion
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

    /**
     * @deprecated
     * @param $name
     * @param $mime
     * @param string $encoding
     */
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

    /**
     * @param $filePath - file path for download
     * @param $name - file name
     * @param $mime - mime type for file
     * @param string $encoding
     */
    public static function setDownloadResponse($filePath, $name = null, $mime = null, $encoding = 'utf-8'){
        if(is_file($filePath)){
            if(!isset($mime)){
                $mime = \yii\helpers\FileHelper::getMimeType($filePath);
            }

            if(!isset($name)){
                $ext = \yii\helpers\FileHelper::getExtensionsByMimeType($mime);
                $name = time() . '.' . $ext[0];
            }

            \Yii::$app->response->format = Response::FORMAT_RAW;
            if (isset($_SERVER["HTTP_USER_AGENT"]) && strstr($_SERVER["HTTP_USER_AGENT"], "MSIE") == false) {
                header("Cache-Control: no-cache");
                header("Pragma: no-cache");
            } else {
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Pragma: public");
            }
            header("Expires: 0");
            header("Content-Encoding: {$encoding}");
            header("Content-Type: {$mime}; charset={$encoding}");
            header("Content-Disposition: attachment; filename={$name}");
            header('Content-Length: ' . filesize($filePath));
            header("Cache-Control: max-age=0");
            readfile($filePath);
        }else{
            throw new Exception("Cannot download this file.");
        }

    }

    public static function getIpAddress(){
        if(isset($_SERVER["HTTP_CF_CONNECTING_IP"])){
            return $_SERVER["HTTP_CF_CONNECTING_IP"];
        }else{
            return \Yii::$app->request->userIP;
        }
    }

    /**
     * @return \Mobile_Detect
     */
    public static function deviceDetect(){
        if(isset(\Yii::$app->devicedetect)){
            return \Yii::$app->devicedetect;
        }else{
            throw new Exception("Please Defind devicedetect to config at bootstrap and component)");
        }
    }

    /**
     * @return bool
     * @throws Exception
     */
    public static function isOnlyMobile(){
        $deviceDetect = self::deviceDetect();
        if($deviceDetect->isMobile() && ! $deviceDetect->isTablet()){
            // because tablet is mobile too.
            return true;
        }else{
            return false;
        }
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


    public static function beginTransaction($connection = null, $islationLevel = null){
        if(!isset($connection)){
            $connection = self::app()->db;
        }

        return $connection->beginTransaction($islationLevel);
    }

    /**
     * get/set cache
     * @return \yii\caching\Cache
     */
    public static function cache(){
        return \Yii::$app->cache;
    }

    /**
     * get cached data and auto store data to cache by key and anonymous function
     * @param $cacheKey
     * @param $function
     * @param null $duration
     * @return cached data
     */
    public static function getAutoCache($cacheKey, $function , $duration = null){
        $data = self::cache()->get($cacheKey);
        if($data === false){
            $data = $function();
            self::cache()->set($cacheKey, $data, $duration);
            return $data;
        }else{
            return $data;
        }
    }


    /**
     * get current user and mapping to db
     * @return \common\models\User
     */
    public static function userDb($cache = true){
//        return $userId = self::user()->identity;
        return User::findIdentity(self::user()->id, $cache);
    }

    /**
     * @param array $composeView
     * @param array $composeParams
     * @param string $senderName
     * @return MessageInterface
     */
    public static function getAutoMail($composeView = null, $composeParams = [], $senderName = null){
        if(!isset($senderName)){
            $senderName = \Yii::$app->name;
        }
        return \Yii::$app->mailer->compose($composeView, $composeParams)
            ->setFrom([\Yii::$app->params['autoEmail'] => $senderName]);
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

    public static function rgb2hex($red, $green, $blue){
        return sprintf("#%02x%02x%02x", $red, $green, $blue);
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
     * support only single model
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
     *
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

    /**
     * @param $validateModelArray - array of validate result of model (can be single or multiple)
     *  ex. [validateModel, validateCollectionOfModels,...]
     *
     * @return array for ajax validate
     */
    public static function responseAjaxValidate($validateModelArray){
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $result = [];
        foreach($validateModelArray as $key => $validateModel){
            $result = ArrayHelper::merge($result, $validateModel);
        }
        return $result;
    }

    public static function setAjaxResponse(){
        \Yii::$app->response->format = Response::FORMAT_JSON;
    }

    /**
     * checking for is yii ajax request
     * @return bool|mixed
     */
    public static function isAjax(){
        return \Yii::$app->request->isAjax;
    }


    /**
     * return the variable need to debug and break app
     * @param $data
     * @param bool|true $end
     */
    public static function debug($data, $end = true){
        echo "<pre>" . print_r($data, true) . "</pre>";
        if($end)
            exit;
    }

    /**
     * return the models need to debug and break app
     * @param $models ActiveRecord[]
     * @param bool|true $end
     */
    public static function debugModels($models, $end = true){

        echo "<pre>";
        foreach($models as $model){
            print_r($model->attributes);
        }
        echo "</pre>";
        if($end)
            exit;
    }

    /**
     * @param $query
     * @param null $db
     * @param bool|true $end
     */
    public static function debugQuery($query, $db = null , $end = true){
        echo "<pre>" . $query->createCommand($db)->rawSql . "</pre>";
        if($end)
            exit;
    }

    /**
     * force hide debug toolbar
     * @param $view View
     */
    public static function hideDebugToolsBar($view){
        if (class_exists('yii\debug\Module')) {
            $view->off(\yii\web\View::EVENT_END_BODY, [\yii\debug\Module::getInstance(), 'renderToolbar']);
        }
    }

    /**
     * supported  get extra http from cloudflare
     * @return array|null
     */
    public static function getCloudflareHttpRequests(){
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
            $result = [];
            $cloudflareParams = [
                'HTTP_CF_CONNECTING_IP', // real visitor ip address
                'HTTP_CF_IPCOUNTRY', // country of visitor
                'HTTP_CF_VISITOR', // this can help you know if its http or https
                'HTTP_CF_RAY', // header traces a website request through Cloudflare's network
            ];
            foreach($cloudflareParams as $key){
                $result[$key] = ArrayHelper::getValue($_SERVER, $key);
            }
            return $result;
        }else{
            return null;
        }
    }




}