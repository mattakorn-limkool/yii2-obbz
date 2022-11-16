<?php
namespace obbz\yii2\widgets\fileupload\behaviors;
use obbz\yii2\utils\ObbzYii;
use yii\base\Behavior;
use yii\db\BaseActiveRecord;
use yii\helpers\FileHelper;

/**
 * @author: Mattakorn Limkool
 *
 */
class MultipleUploadBehavior extends Behavior
{
//    public $thumbs;
    public $attributes;

    public function init(){
        if($this->attributes == null){
            throw new InvalidConfigException('Please define $attributes');
        }


        parent::init();
    }

    public function events()
    {
        return [
//            BaseActiveRecord::EVENT_BEFORE_VALIDATE => 'beforeValidate',
//            BaseActiveRecord::EVENT_BEFORE_INSERT => 'beforeSave',
//            BaseActiveRecord::EVENT_BEFORE_UPDATE => 'beforeSave',
            BaseActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
            BaseActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',
//            BaseActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
        ];
    }

    public function getFirstImageUrl($field, $thumb=null){
        $images = $this->getAllImageUrl($field, $thumb);
        if(isset($images[0])){
            return $images[0];
        }else{
            return '';
        }
    }

    public function getAllImageUrl($field, $thumb = null){
        $directory = $this->getMultipleUploadPath($field, $this->owner->id, $thumb);
        $urlDirectory = $this->getMultipleUploadUrl($field, $this->owner->id, $thumb);

        $files = [];
        if(file_exists($directory)){
            $images = scandir($directory);
            foreach($images as $fileName){
                if(!in_array($fileName,array(".","..")) && !is_dir($directory . $fileName)){
                    $files[] = $urlDirectory . $fileName;
                }
            }
        }

        return $files;

    }

    public function getMultipleUploadPath($field, $id, $thumb=null){
        $path =  \Yii::getAlias('@uploadPath') . DIRECTORY_SEPARATOR .
        $this->owner->tableName() . DIRECTORY_SEPARATOR . $field . DIRECTORY_SEPARATOR
        . $id. DIRECTORY_SEPARATOR;

        if($thumb){
            $path .= $thumb . DIRECTORY_SEPARATOR;
        }
        return $path;
    }

    public function getMultipleUploadUrl($field, $id, $thumb=null){
        $url =  \Yii::getAlias('@uploadUrl'). '/' .
        $this->owner->tableName() . '/' . $field .'/'
        . $id . '/' ;

        if($thumb){
            $url .= $thumb . '/';
        }
        return $url;
    }

    /**
     * @param array $fields clear all upload file by session when empty
     */
    public function resetSessionFiles($fields = []){
        if(empty($fields)){
            $fields = array_keys($this->attributes);
        }
        foreach($fields as $field){
            $sessionDirectory = $this->getMultipleUploadPath($field, \Yii::$app->session->id);
            if(is_dir($sessionDirectory)){ // on new record
                FileHelper::removeDirectory($sessionDirectory);

            }
        }

    }

    public function afterSave()
    {
        foreach($this->attributes as $attribute => $config){
            $sessionDirectory = $this->getMultipleUploadPath($attribute, \Yii::$app->session->id);
            if(file_exists($sessionDirectory)){ // on new record
                $newDirectory = $this->getMultipleUploadPath($attribute, $this->owner->id);
                rename($sessionDirectory, $newDirectory);
            }
        }
    }


}