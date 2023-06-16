<?php
namespace obbz\yii2\widgets\fileupload\behaviors;
use Codeception\Lib\Interfaces\ActiveRecord;
use obbz\yii2\admin\models\FlexibleModule;
use obbz\yii2\admin\models\FlexibleModuleItem;
use obbz\yii2\models\CoreActiveRecord;
use obbz\yii2\utils\ObbzYii;
use yii\base\Behavior;
use yii\db\ActiveQuery;
use yii\db\BaseActiveRecord;
use yii\helpers\FileHelper;

/**
 * @author: Mattakorn Limkool
 *
 */
class MultipleUploadDbBehavior extends Behavior
{
    public $itemModelClass;
//    public $attributes;

    public function init(){
        if($this->itemModelClass == null){
            throw new InvalidConfigException('Please define $itemModelClass');
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

    public function getUploadItemModel(){
        return $this->itemModelClass;
    }

    /**
     * @return FlexibleModuleItem[]
     */
    public function getRelateItems(){
        $itemClass = $this->itemModelClass;
        return $itemClass::find()->andWhere(['flexible_module_id'=>$this->owner->id])->all();
    }

    public function resetItemsByUserSession($hardReset = true){
        $session = \Yii::$app->session->id;
        /** @var CoreActiveRecord $itemClass */
        $itemClass =  $this->getUploadItemModel();
        if($hardReset){
            $itemClass::deleteAll(['user_session'=>$session, 'flexible_module_id'=>null]);
        }else{
            $items = $this->getNoParentItems();
            foreach($items as $item){
                $item->markDelete();
            }
        }

    }


    /**
     * @return FlexibleModuleItem[]
     */
    public function getNoParentItems(){
        $itemClass = $this->itemModelClass;
        $session = \Yii::$app->session->id;
        return  $itemClass::findAll(['user_session'=>$session, 'flexible_module_id'=>null]);
    }


    public function afterSave()
    {
        $session = \Yii::$app->session->id;
        /** @var CoreActiveRecord $itemClass */
        $itemClass =  $this->getUploadItemModel();
        $itemClass::updateAll(
            ['flexible_module_id'=>$this->owner->id],
            ['user_session'=>$session, 'flexible_module_id'=>null]
        );
    }
//    public function getFirstImageUrl($field, $thumb=null){
//        $images = $this->getAllImageUrl($field, $thumb);
//        if(isset($images[0])){
//            return $images[0];
//        }else{
//            return '';
//        }
//    }
//
//    public function getAllImageUrl($field, $thumb = null){
//        $directory = $this->getMultipleUploadPath($field, $this->owner->id, $thumb);
//        $urlDirectory = $this->getMultipleUploadUrl($field, $this->owner->id, $thumb);
//
//        $files = [];
//        if(file_exists($directory)){
//            $images = scandir($directory);
//            foreach($images as $fileName){
//                if(!in_array($fileName,array(".","..")) && !is_dir($directory . $fileName)){
//                    $files[] = $urlDirectory . $fileName;
//                }
//            }
//        }
//
//        return $files;
//
//    }





}