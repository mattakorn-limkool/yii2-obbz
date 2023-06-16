<?php

namespace obbz\yii2\admin\models;

use common\models\FlexibleModuleItem;
use obbz\yii2\utils\ArrayHelper;
use obbz\yii2\utils\Html;
use obbz\yii2\utils\ObbzYii;
use obbz\yii2\widgets\fileupload\behaviors\MultipleUploadDbBehavior;

/**
 * @var FlexibleModuleItem[] $items
*/

class FlexibleModule extends \obbz\yii2\admin\models\base\FlexibleModuleBase
{
    const KEY_IMAGE_SLIDE = 'image_slide';
    const KEY_IMAGE_GALLERY = 'image_gallery';
    const KEY_YOUTUBE_GALLERY = 'youtube_gallery';

    const SCENARIO_UPLOAD_IMAGE = "upload_image";

    public $columnPatterns = [
        'col-lg-12' => 'Full Width Column',
        'col-lg-6' => '2 Columns',
        'col-lg-4 col-sm-6' => '3 Columns',
        'col-lg-3 col-md-4' => '4 Columns',
    ];

    public $markerCssClass = 'flexible-module';
    public $uploadItems;

    const DEFAULT_THUMBS = [
        'thumb'=> ['width'=>300]
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

            [['uploadItems'], 'image', 'extensions' => 'jpg, jpeg',
                'maxSize' => \Yii::$app->params['upload.maxSize'],'on'=>[self::SCENARIO_UPLOAD_IMAGE]],
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
            'multipleUploadImages' => [
                'class' => MultipleUploadDbBehavior::class,
                'itemModelClass' => FlexibleModuleItem::class,
            ],

        ]);
    }

    public function attributeLabels(){
        return array_merge(parent::attributeLabels(),[
            'key_name' => 'Module Type',
            'column_pattern' => 'Column',
            'title' => 'Name',
        ]);
    }

    public static function getKeyList(){
        return [
            self::KEY_IMAGE_SLIDE => 'Images Slide',
            self::KEY_IMAGE_GALLERY => 'Images Gallery',
            self::KEY_YOUTUBE_GALLERY => 'Youtube List',
        ];
    }

    public function getRteMarker(){
        $result = '';
        if($this->id){

            $options = [
                'class' => $this->markerCssClass,
                'data-flexmodule-id'=>$this->id,
                'frameborder' => 0,
                'src' => \yii\helpers\Url::to(['ck-view', 'id'=>$this->id], true)
            ];

            $result = Html::tag('iframe', '', $options);
//            $title = ArrayHelper::getValue(self::getKeyList(), $this->key_name, '') .
//                ' : ' . ArrayHelper::getValue($this->columnPatterns, $this->column_pattern, '');
//
//            if($this->title){
//                $title .= ' : ' . $this->title;
//            }
//            $content  = Html::tag('div', $title, ['class'=>'title']);
//            $result = Html::tag('div', $content, $options);
        }

        return $result;
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