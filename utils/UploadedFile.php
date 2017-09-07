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

    public static function getInstanceByBase64($base64file){
        self::$isBase64 = true;
        $result = self::loadBase64File($base64file);
        return new static($result);
    }

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

    public function saveAs($file, $deleteTempFile = true)
    {
        if(self::$isBase64){
            if ($deleteTempFile) {
                return rename($this->tempName, $file);
            }else{ // fixed for copy tempName always.
                return copy($this->tempName, $file);
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