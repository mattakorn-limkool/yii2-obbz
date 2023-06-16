<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\widgets\fileupload\actions;

use Imagine\Image\ManipulatorInterface;
use obbz\yii2\admin\models\FlexibleModuleItem;
use obbz\yii2\models\CoreBaseActiveRecord;
use obbz\yii2\utils\ArrayHelper;
use obbz\yii2\utils\ObbzYii;
use obbz\yii2\widgets\fileupload\behaviors\MultipleUploadDbBehavior;
use yii\base\Action;
use yii\helpers\FileHelper;
use yii\helpers\Inflector;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\imagine\Image;
use yii\web\UploadedFile;

class MultipleUploadDbAction extends Action
{

    public $modelClass;
    public $deleteUrl = ['image-delete'];
    public $scenario = null;
    public $itemConf = [
        'imageField' => 'image',
        'titleField' => 'title',
        'scenario' => [
            'create' => CoreBaseActiveRecord::SCENARIO_BE_CREATE,
            'update' => CoreBaseActiveRecord::SCENARIO_BE_UPDATE,
        ]
    ];
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
    public function run($field, $item_id = null, $id = null){


//        $folderPath = isset($id) ? $id : \Yii::$app->session->id;
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

        /** @var FlexibleModuleItem $itemModel */
        $itemClass = $model->getUploadItemModel();
        $itemModel = new $itemClass;
        if($item_id && $id){
            $mode = 'update';
        }else{
            $mode = 'create';
        }

        $itemModel->setScenario($this->itemConf['scenario'][$mode]);
        $itemImageField = ArrayHelper::getValue($this->itemConf, 'imageField');
        $itemTitleField = ArrayHelper::getValue($this->itemConf, 'titleField');

        $deleteUrlConf = $this->deleteUrl;
        $deleteUrlConf['field'] = $field;

        if ($model->$field) { // on upload
            $itemModel->$itemImageField = $model->$field;
            $itemModel->file_size = $model->$field->size;
            $fileName = '';
            if(isset($model->$field->name)){
                $baseName = preg_replace('/\.\w+$/', '', $model->$field->name);
                $itemModel->$itemTitleField = Inflector::titleize($baseName);
                $fileName = $itemModel->$itemTitleField;
            }

            if(isset($id)){
                $itemModel->flexible_module_id = $id;
            }else{
                $itemModel->user_session =  \Yii::$app->session->id;
            }


            if($itemModel->save()){
                $deleteUrlConf['id'] = $id;
                $deleteUrlConf['item_id'] = $itemModel->id;
                $deleteUrl = Url::to($deleteUrlConf);

                return Json::encode([
                    'files' => [
                        [
                            'name' =>  $itemModel->$itemTitleField ,
                            'size' => $itemModel->file_size,
                            'url' => $itemModel->getThumbUploadUrl($itemImageField),
                            'thumbnailUrl' => $itemModel->getThumbUploadUrl($itemImageField),
                            'deleteUrl' => $deleteUrl,
                            'deleteType' => 'POST',
                        ],
                    ],
                ]);
            }else{
                return Json::encode([
                    'files'=>[
                        [
                            'error' => $itemModel->getFirstErrors()
                        ]
                    ]
                ]);
            }

        }else{ // get data on update
            if(!empty($id)){
                $model->id = $id;
                $items = $model->getRelateItems();

                $files = [];
                foreach($items as $item){
                    $deleteUrlConf['id'] = $id;
                    $deleteUrlConf['item_id'] = $item->id;
                    $deleteUrl = Url::to($deleteUrlConf);

                    $files[] = [
                        'name' =>  $item->$itemTitleField ,
                        'size' => $item->file_size,
                        'url' => $item->getThumbUploadUrl($itemImageField),
                        'thumbnailUrl' => $item->getThumbUploadUrl($itemImageField),
                        'deleteUrl' => $deleteUrl,
                        'deleteType' => 'POST',
                    ];
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
            if(isset($behavior['class']) && $behavior['class'] == MultipleUploadDbBehavior::class){
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