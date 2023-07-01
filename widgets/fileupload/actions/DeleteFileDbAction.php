<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\widgets\fileupload\actions;

use obbz\yii2\admin\models\FlexibleModuleItem;
use obbz\yii2\behaviors\UploadImageBehavior;
use obbz\yii2\models\CoreBaseActiveRecord;
use obbz\yii2\utils\ArrayHelper;
use obbz\yii2\utils\ObbzYii;
use yii\base\Action;
use yii\base\InvalidConfigException;
use yii\helpers\FileHelper;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\imagine\Image;
use yii\web\UploadedFile;
use obbz\yii2\widgets\fileupload\behaviors\MultipleUploadBehavior;

class DeleteFileDbAction extends Action
{
    public $modelItemClass;
    public $itemRefField;
    public $deleteUrl = null;
    public $scenario = null;
    public $defaultThumb = null;


    public function init(){
        if($this->modelItemClass == null){
            throw new InvalidConfigException('Please define $modelItemClass');
        }

        if($this->itemRefField == null){
            throw new InvalidConfigException('Please define $itemRefField');
        }

        if($this->deleteUrl == null){
            $this->deleteUrl = ['/'. $this->controller->id . '/' . $this->id];
        }

        parent::init();
    }

    /**
     * @param $name
     * @param null $id
     * @return string
     */
    public function run($item_id){
        $output = [];
        $itemRefField = $this->itemRefField;
        /** @var FlexibleModuleItem $model */
        $modelClass = $this->modelItemClass;
        $model = $modelClass::find()->pk($item_id);

        if($model){
            if($this->scenario){
                $model->setScenario($this->scenario);
            }
            $id = $model->$itemRefField;
            $model->delete();

            /** @var FlexibleModuleItem[] $models */
            $models = $modelClass::find()->andWhere([$itemRefField=>$id])->all();


            foreach ($models as $item) {
                $deleteUrlConf = $this->deleteUrl;
                $deleteUrlConf['item_id'] = $item->id;
                $deleteUrl = Url::to($deleteUrlConf);
                $output['files'][] = [
                    'name' => $item->title,
                    'size' => $item->file_size,
                    'url' => $item->getThumbUploadUrl('image'),
                    'thumbnailUrl' => $item->getThumbUploadUrl('image'),
                    'deleteUrl' => $deleteUrl,
                    'deleteType' => 'POST',
                ];
            }


        }else{
            $output = ['error'=>'Error, please try again later'];
        }



        return Json::encode($output);

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

}