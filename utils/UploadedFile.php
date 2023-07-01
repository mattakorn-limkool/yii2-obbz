<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\utils;


use yii\helpers\FileHelper;

class UploadedFile extends \yii\web\UploadedFile
{
    private static $_files;
    private static $isBase64 = false;
    private static $isUrl = false;
    private static $isLocalPath = false;

    #region get by url

    /**
     * get instance by url
     * @param $url
     * @return static
     */
    public static function getInstanceByUrl($url, $ignoreError = false){
        self::$isUrl = true;
        $result = self::loadUrlFile($url, $ignoreError );
        return new static($result);
    }

    private static function loadUrlFile($url, $ignoreError = false)
    {
        if($ignoreError){
            $fileSource = @file_get_contents($url);
        }else{
            $fileSource = file_get_contents($url);
        }

        if($fileSource == false)
            return ['error'=>1];

        $f = finfo_open();
        $mimeType = finfo_buffer($f, $fileSource, FILEINFO_MIME_TYPE);

        $extensions = FileHelper::getExtensionsByMimeType($mimeType);
        $ext = $extensions[count($extensions) -1];
        $tmpName = uniqid() . '.'.$ext;

        $tmpPath = \Yii::getAlias('@runtime') . '/upload/' ;
        $tmpPathName = $tmpPath . $tmpName;
        if(!is_dir($tmpPath)){
            FileHelper::createDirectory($tmpPath, 0777);
        }
        $byte = file_put_contents($tmpPathName, $fileSource);


        $file = [
            'name' => $tmpName,
            'tempName' => $tmpPathName,
            'type' => $mimeType,
            'size' => $byte,
            'error' => ($byte === false)? 1 : 0,
        ];
        return $file;

    }

    #endregion


    #region get by base 64

    /**
     * @param $base64file  - base64file string
     * @return static - return it's self
     */
    public static function getInstanceByBase64($base64file){
        self::$isBase64 = true;
        $result = self::loadBase64File($base64file);
        return new static($result);
    }

    /**
     * @param $base64file
     * @return array
     * @throws \yii\base\Exception
     */
    private static function loadBase64File($base64file)
    {
        $fileSource = $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64file));
        $f = finfo_open();
        $mimeType = finfo_buffer($f, $fileSource, FILEINFO_MIME_TYPE);
        $extensions = FileHelper::getExtensionsByMimeType($mimeType);
        $ext = $extensions[count($extensions) -1];
        $tmpName = uniqid() . '.'.$ext;

        $tmpPath = \Yii::getAlias('@runtime') . '/upload/' ;
        $tmpPathName = $tmpPath . $tmpName;
        if(!is_dir($tmpPath)){
            FileHelper::createDirectory($tmpPath, 0777);
        }

        $byte = file_put_contents($tmpPathName, $fileSource);
//        $size = filesize ( $tmpPath );


        $file = [
            'name' => $tmpName,
            'tempName' => $tmpPathName,
            'type' => $mimeType,
            'size' => $byte,
            'error' => ($byte === false)? 1 : 0,
        ];
        return $file;

    }

    #endregion

    #region get by local path

    /**
     * @param $url
     * @return static
     */
    public static function getInstanceByPath($localPath, $ignoreError = false){
        self::$isLocalPath = true;
        $result = self::loadUrlFile($localPath, $ignoreError);
        return new static($result);
    }


    #endregion


    public function saveAs($file, $deleteTempFile = true)
    {
        if(self::$isBase64 || self::$isUrl || self::$isLocalPath){
            if(is_file($this->tempName)){
                if ($deleteTempFile) {
                    return rename($this->tempName, $file);
                }else{ // fixed for copy tempName always.
                    return copy($this->tempName, $file);
                }
            }
        }else{
            if ($this->error == UPLOAD_ERR_OK) {
                if ($deleteTempFile) {
                    return move_uploaded_file($this->tempName, $file);
                } elseif (is_uploaded_file($this->tempName)) {
                    return copy($this->tempName, $file);
                }
            }
        }

        return false;
    }
}