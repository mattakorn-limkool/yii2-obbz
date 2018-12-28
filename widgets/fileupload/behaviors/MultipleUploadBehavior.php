<?php
namespace obbz\yii2\widgets\fileupload\behaviors;
use yii\base\Behavior;

/**
 * @author: Mattakorn Limkool
 *
 */
class MultipleUploadBehavior extends Behavior
{
    public $thumbs;


    public function init(){
        if($this->thumbs == null){
            throw new InvalidConfigException('Please define $thumbs');
        }


        parent::init();
    }

    public function getFirstImageUrl($field){
        $images = $this->getAllImageUrl($field);
        if(isset($images[0])){
            return $images[0];
        }else{
            return '';
        }
    }

    public function getAllImageUrl($field){
        $directory = $this->getMultipleUploadPath($field, $this->owner->id);
        $urlDirectory = $this->getMultipleUploadUrl($field, $this->owner->id);


        $files = [];
        if(file_exists($directory)){
            $images = scandir($directory);
            foreach($images as $fileName){
                if(!in_array($fileName,array(".",".."))){
                    $files[] = $urlDirectory . $fileName;
                }
            }
        }

        return $files;

    }

    public function getMultipleUploadPath($field, $id){
        return \Yii::getAlias('@uploadPath') . DIRECTORY_SEPARATOR .
        $this->owner->tableName() . DIRECTORY_SEPARATOR . $field . DIRECTORY_SEPARATOR
        . $id. DIRECTORY_SEPARATOR;
    }

    public function getMultipleUploadUrl($field, $id){
        return \Yii::getAlias('@uploadUrl'). '/' .
        $this->owner->tableName() . '/' . $field .'/'
        . $id . '/' ;
    }

}