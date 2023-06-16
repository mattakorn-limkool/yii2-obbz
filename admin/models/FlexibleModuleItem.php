<?php

namespace obbz\yii2\admin\models;

use obbz\yii2\utils\ArrayHelper;
use obbz\yii2\utils\ObbzYii;
/**
*/

class FlexibleModuleItem extends \obbz\yii2\admin\models\base\FlexibleModuleItemBase
{

//    const SCENARIO_UPLOAD_IMAGE = "upload_image";

    const DEFAULT_THUMBS = [
        'thumb'=> ['width'=>150, 'quanlity'=>100],
        'xs'=> ['width'=>300, 'quanlity'=>100],
        'md'=> ['width'=>600, 'quanlity'=>100],
        'lg'=> ['width'=>1200, 'quanlity'=>100],
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
        $thumbWidth = ArrayHelper::getValue(self::DEFAULT_THUMBS, 'thumb.width');
        $thumbHeight = ArrayHelper::getValue(self::DEFAULT_THUMBS, 'thumb.height');
        return array_merge(parent::rules(),[
			['image', 'image', 'extensions' => 'jpg, jpeg',
                'maxSize' => \Yii::$app->params['upload.maxSize'],
                //'minWidth'=> $thumbWidth, 'minHeight' => $thumbHeight,
                'on'=>$this->scenarioCU()],
            //[['field'], 'required', 'on'=>$this->scenarioCU()],
        ]);
    }

	public function behaviors(){
        return array_merge(parent::behaviors(),[
			'uploadImage' => $this->defaultImgBehavior('image', self::DEFAULT_THUMBS, ['scenarios' => $this->scenarioCU()]) ,
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