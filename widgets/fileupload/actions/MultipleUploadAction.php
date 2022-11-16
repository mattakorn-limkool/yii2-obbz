<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\widgets\fileupload\actions;

use Imagine\Image\ManipulatorInterface;
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
    public $deleteUrl = ['image-delete'];
    public $scenario = null;
    public $defaultThumb = null;

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
                $imageConfig = $this->getImageConfig($model, $field);

                if($imageConfig){
                    foreach($imageConfig as $thumbName => $thumbConf){
                        $thumbPath = $directory  . $thumbName ;
                        if(!is_dir($thumbPath)){
                            FileHelper::createDirectory($thumbPath);
                        }
                        $this->generateImageThumb($thumbConf, $filePath, $thumbPath . '/'. $fileName);
                    }
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
                $subDirectory = $directory;
                $subUrl = $urlDirectory;
                if(isset($this->defaultThumb)){
                    $subDirectory .=  $this->defaultThumb . DIRECTORY_SEPARATOR;
                    $subUrl .=  $this->defaultThumb . '/' ;
                }

                $foundImages = scandir($subDirectory);

                $files = [];
                foreach($foundImages as $fileName){
                    if(!in_array($fileName,array(".","..")) && !is_dir($subDirectory. $fileName)){
                        $path =  $subUrl . $fileName;
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
                $conf = ArrayHelper::getValue($behavior, 'attributes.'. $field .'.thumbs');
                if(isset($conf)){
                    return $conf;
                }
            }
        }
    }

    protected function generateImageThumb($config, $path, $thumbPath)
    {

        $width = ArrayHelper::getValue($config, 'width');
        $height = ArrayHelper::getValue($config, 'height');
        $quality = ArrayHelper::getValue($config, 'quality', 100);
        $mode = ArrayHelper::getValue($config, 'mode', ManipulatorInterface::THUMBNAIL_OUTBOUND);
        $bg_color = ArrayHelper::getValue($config, 'bg_color', 'FFF');

        if (!$width || !$height) {
            $image = Image::getImagine()->open($path);
            $ratio = $image->getSize()->getWidth() / $image->getSize()->getHeight();
            if ($width) {
                $height = ceil($width / $ratio);
            } else {
                $width = ceil($height * $ratio);
            }
        }
        // Fix error "PHP GD Allowed memory size exhausted".
        $defaulMemLimit = ini_get ('memory_limit');
        ini_set('memory_limit', $this->memeryLimit);
        Image::$thumbnailBackgroundColor = $bg_color;
        Image::thumbnail($path, $width, $height, $mode)->save($thumbPath, ['quality' => $quality]);
        ini_set ('memory_limit',$defaulMemLimit);
    }


}