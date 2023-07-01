<?php

namespace obbz\yii2\admin\models;

use obbz\yii2\utils\ArrayHelper;
use obbz\yii2\utils\ObbzYii;
/**
*/

class FlexibleModuleItem extends \obbz\yii2\admin\models\base\FlexibleModuleItemBase
{

//    const SCENARIO_UPLOAD_IMAGE = "upload_image";

    public $defaultThumbs = [
        'thumb'=> ['width'=>200, 'quanlity'=>100],
        'xs'=> ['width'=>400, 'quanlity'=>100],
        'md'=> ['width'=>800, 'quanlity'=>100],
        'lg'=> ['width'=>1600, 'quanlity'=>100],
    ];

    public $columnThumbConf = [
        FlexibleModule::COL_1 => 'lg',
        FlexibleModule::COL_2 => 'md',
        FlexibleModule::COL_3 => 'xs',
        FlexibleModule::COL_4 => 'xs',
    ];

    public $autoDateFields = [
//        ['field' =>'created_time', 'inputType'=>self::AUTODATE_TYPE_DATETIME, 'scenarios'=>[self::SCENARIO_BE_CREATE, self::SCENARIO_BE_UPDATE]],
//        ['field' =>'modify_time', 'inputType'=>self::AUTODATE_TYPE_DATETIME, 'scenarios'=>[self::SCENARIO_BE_CREATE, self::SCENARIO_BE_UPDATE]],
    ];

//    public function scenarioCreate(){
//        return array_merge(parent::scenarioCreate(), []);
//    }
//
//    public function scenarioUpdate(){
//        return array_merge(parent::scenarioUpdate(), []);
//    }


    public function rules(){
        $thumbWidth = ArrayHelper::getValue($this->defaultThumbs, 'thumb.width');
        $thumbHeight = ArrayHelper::getValue($this->defaultThumbs, 'thumb.height');
        return array_merge(parent::rules(),[
			['image', 'image', 'extensions' => 'jpg, jpeg, webp',
                'maxSize' => \Yii::$app->params['upload.maxSize'],
                //'minWidth'=> $thumbWidth, 'minHeight' => $thumbHeight,
                'on'=>$this->scenarioCU()],
            //[['field'], 'required', 'on'=>$this->scenarioCU()],
        ]);
    }

	public function behaviors(){
        return array_merge(parent::behaviors(),[
			'uploadImage' => $this->defaultImgBehavior('image', $this->defaultThumbs, ['scenarios' => $this->scenarioCU()]) ,
//            'translateable' => [
//                'class' => \obbz\yii2\behaviors\TranslationBehavior::class,
//                'translationAttributes' => ['title','detail'],
//            ],
			// other behavior
        ]);
    }

    public function attributeLabels(){
        return array_merge(parent::attributeLabels(),[]);
    }

    public function getThumbByColumn($columnName = null){
        if(!isset($columnName)){
            // todo - get parent model
        }
        $thumb = ArrayHelper::getValue($this->columnThumbConf, $columnName);
        return $this->getThumbUploadUrl('image', $thumb);
    }

    public function getFullThumb(){
        return $this->getThumbUploadUrl('image', 'lg');
    }


//	 public function beforeValidate() {
//        if(parent::beforeValidate()) {
//            // your code here
//            return true;
//        }else{
//            return false;
//        }
//    }

//    public function afterValidate(){
//        // your code here
//        parent::afterValidate();
//    }

//    public function beforeSave($insert){
//        if (parent::beforeSave($insert)) {
//           // your code here
//            return true;
//        } else {
//            return false;
//        }
//    }

//    public function afterSave($insert, $changedAttributes){
//        // your code here
//        parent::afterSave($insert, $changedAttributes);
//    }

//    public function afterFind(){
//        parent::afterFind();
//        // your code here
//
//    }

   
}