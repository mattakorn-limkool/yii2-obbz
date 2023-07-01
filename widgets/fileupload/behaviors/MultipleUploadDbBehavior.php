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
    public $itemRefField;
//    public $attributes;

    public function init(){
        if($this->itemModelClass == null){
            throw new InvalidConfigException('Please define $itemModelClass');
        }

        if($this->itemModelClass == null){
            throw new InvalidConfigException('Please define $itemRefField');
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
    public function getRelateItems($limit = null){
        $itemClass = $this->itemModelClass;
        $query = $itemClass::find()->andWhere([$this->itemRefField=>$this->owner->id])->be();
        if($limit)
            $query->limit($limit);
        return $query->all();
    }

    public function getFirstRelateItem(){
        $itemClass = $this->itemModelClass;
       return $itemClass::find()->andWhere([$this->itemRefField=>$this->owner->id])->be()->one();
    }

    /**
     * @return FlexibleModuleItem[]
     */
    public function getFeRelateItems($limit = null){
        $itemClass = $this->itemModelClass;
        $query = $itemClass::find()->andWhere([$this->itemRefField=>$this->owner->id])->fe();
        if($limit)
            $query->limit($limit);
        return $query->all();
    }

    public function getFirstFeRelateItem(){
        $itemClass = $this->itemModelClass;
        return $itemClass::find()->andWhere([$this->itemRefField=>$this->owner->id])->fe()->one();
    }



    public function resetItemsByUserSession($hardReset = true){
        $session = \Yii::$app->session->id;
        /** @var CoreActiveRecord $itemClass */
        $itemClass =  $this->getUploadItemModel();
        if($hardReset){
            $itemClass::deleteAll(['user_session'=>$session, $this->itemRefField=>null]);
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
        return  $itemClass::findAll(['user_session'=>$session, $this->itemRefField=>null]);
    }


    public function afterSave()
    {
        $session = \Yii::$app->session->id;
        /** @var CoreActiveRecord $itemClass */
        $itemClass =  $this->getUploadItemModel();
        $itemClass::updateAll(
            [$this->itemRefField=>$this->owner->id],
            ['user_session'=>$session, $this->itemRefField=>null]
        );
    }



}