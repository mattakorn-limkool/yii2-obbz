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
use yii\helpers\Url;
use yii\imagine\Image;
use yii\web\UploadedFile;

class MultipleUploadAction extends Action
{

    public $modelClass;
    public $deleteUrl;
    public $scenario = null;

    // advanced config
    public $memeryLimit = '512M';


    public function init(){
        if($this->modelClass == null){
            throw new InvalidConfigException('Please define $modelClass');
        }


        if($this->deleteUrl == null){
            throw new InvalidConfigException('Please define $deleteUrl');
        }


        parent::init();
    }
    /**
     * @param $field - field for upload model
     * @param null $id - id of model
     */
    public function run($field, $id = null){
        $folderPath = isset($id) ? $id : \Yii::$app->session->id;
        /** @var CoreBaseActiveRecord $model */
        $model = new $this->modelClass;

        if($this->scenario){
            $model->setScenario($this->scenario);

        }


        $model->$field = UploadedFile::getInstance($model, $field);

        if(!$model->validate()){

            $message = $model->getFirstError($field);
            return Json::encode([
                'files'=>[
                    [
                        'error' => $message
                    ]
                ]
            ]);
        }
//        ObbzYii::debug($model->$field);

        $directory = $model->getMultipleUploadPath($field, $folderPath);
        $urlDirectory = $model->getMultipleUploadUrl($field, $folderPath);

        $deleteUrlConf = $this->deleteUrl;
        $deleteUrlConf['field'] = $field;
        $deleteUrlConf['id'] = $folderPath;

        if ($model->$field) { // on upload

            if (!is_dir($directory)) {
                FileHelper::createDirectory($directory);
            }

            $uid = uniqid(time(), true);
            $fileName = $uid . '.' . $model->$field->extension;
            $filePath = $directory . $fileName;


            if ($model->$field->saveAs($filePath)) {
                $path =  $urlDirectory . $fileName;
                // todo - make thumbnail via behavior config
                // now generate 1 thumbnail via rules config
                $imageConfig = $this->getImageConfig($model, $field);
                if($imageConfig){
                    // resize image
                    $width = $imageConfig['width'];
                    $image = Image::getImagine()->open($filePath);
                    $ratio = $image->getSize()->getWidth() / $image->getSize()->getHeight();
                    $height = ceil($width / $ratio);
                    ini_set('memory_limit', $this->memeryLimit);
                    Image::thumbnail($filePath, $width, $height)->save($filePath, ['quality' => 100]);
                }
                $deleteUrlConf['name'] = $fileName;
                $deleteUrl = Url::to($deleteUrlConf);
                // end resize
                return Json::encode([
                    'files' => [
                        [
                            'name' => $fileName,
                            'size' => $model->$field->size,
                            'url' => $path,
                            'thumbnailUrl' => $path,
                            'deleteUrl' => $deleteUrl,
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
                        $deleteUrlConf['name'] = $fileName;
                        $deleteUrl = Url::to($deleteUrlConf);
                        $files[] = [
                            'name' => $fileName,
//                            'size' => $model->$field->size,
                            'url' => $path,
                            'thumbnailUrl' => $path,
                            'deleteUrl' => $deleteUrl,
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

    public function getImageConfig($model, $field){
        $modelBehaviors = $model->behaviors();
        foreach($modelBehaviors as $behavior){

            if(isset($behavior['class']) && $behavior['class'] == MultipleUploadBehavior::class){

                if(isset($behavior['attributes']) && $behavior['attributes'] == $field){
                    return ArrayHelper::getValue($behavior, 'thumbs.thumb');
                }
            }
        }
    }


}