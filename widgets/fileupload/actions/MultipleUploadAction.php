<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\widgets\fileupload\actions;

use obbz\yii2\models\CoreBaseActiveRecord;
use obbz\yii2\utils\ArrayHelper;
use obbz\yii2\utils\ObbzYii;
use obbz\yii2\widgets\fileupload\behaviors\MultipleUploadBehavior;
use yii\base\Action;
use yii\helpers\FileHelper;
use yii\helpers\Json;
use yii\imagine\Image;
use yii\web\UploadedFile;

class MultipleUploadAction extends Action
{

    public $modelClass;
    public $field;
    public $scenario = null;


    public function init(){
        if($this->modelClass == null){
            throw new InvalidConfigException('Please define $modelClass');
        }
        if($this->field == null){
            throw new InvalidConfigException('Please define $field');
        }


        parent::init();
    }
    /**
     * @param null $id - id of model
     */
    public function run($id = null){
        $folderPath = isset($id) ? $id : \Yii::$app->session->id;
        /** @var CoreBaseActiveRecord $model */
        $model = new $this->modelClass;

        if($this->scenario){
            $model->setScenario($this->scenario);
        }

        $uploadFile = UploadedFile::getInstance($model, $this->field);

        if(!$model->validate()){
            $message = $model->getFirstError($this->field);

            return Json::encode([
                'files'=>[
                    [
                        'error' => $message
                    ]
                ]
            ]);
        }

        $directory = $model->getMultipleUploadPath($this->field, $id);
        $urlDirectory = $model->getMultipleUploadUrl($this->field, $id);

        if ($uploadFile) { // on upload
            if (!is_dir($directory)) {
                FileHelper::createDirectory($directory);
            }

            $uid = uniqid(time(), true);
            $fileName = $uid . '.' . $uploadFile->extension;
            $filePath = $directory . $fileName;


            if ($uploadFile->saveAs($filePath)) {
                $path =  $urlDirectory . $fileName;
                // todo - make thumbnail via behavior config
                // now generate 1 thumbnail via rules config
                $imageConfig = $this->getImageConfig($model);
                if($imageConfig){
                    // resize image
                    $width = $imageConfig['width'];
                    $image = Image::getImagine()->open($filePath);
                    $ratio = $image->getSize()->getWidth() / $image->getSize()->getHeight();
                    $height = ceil($width / $ratio);
                    ini_set('memory_limit', '512M');
                    Image::thumbnail($filePath, $width, $height)->save($filePath, ['quality' => 100]);
                }

                // end resize
                return Json::encode([
                    'files' => [
                        [
                            'name' => $fileName,
                            'size' => $uploadFile->size,
                            'url' => $path,
                            'thumbnailUrl' => $path,
                            'deleteUrl' => 'image-delete?name=' . $fileName . '&id=' . $id,
                            'deleteType' => 'POST',
                        ],
                    ],
                ]);
            }
        }else{ // on init
            if(!empty($id)){
                $foundImages = scandir($directory);
                $files = [];
                foreach($foundImages as $fileName){
                    if(!in_array($fileName,array(".",".."))){
                        $path =  $urlDirectory . $fileName;
                        $files[] = [
                            'name' => $fileName,
//                            'size' => $uploadFile->size,
                            'url' => $path,
                            'thumbnailUrl' => $path,
                            'deleteUrl' => 'image-delete?name=' . $fileName . '&id=' . $id,
                            'deleteType' => 'POST',
                        ];
                    }
                }
                return Json::encode([
                    'files' => $files
                ]);
            }
        }

    }

    public function getImageConfig($model){
        $modelBehaviors = $model->behaviors();
        foreach($modelBehaviors as $behavior){

            if(isset($behavior['class']) && $behavior['class'] == MultipleUploadBehavior::class){
                if(isset($behavior['attribute']) && $behavior['attribute'] == $this->field){
                    return ArrayHelper::getValue($behavior, 'thumbs.thumb');
                }
            }
        }
    }
}