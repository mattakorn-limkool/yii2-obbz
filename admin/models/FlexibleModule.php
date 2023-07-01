<?php

namespace obbz\yii2\admin\models;

use obbz\yii2\admin\models\FlexibleModuleItem;
use obbz\yii2\utils\ArrayHelper;
use obbz\yii2\utils\Html;
use obbz\yii2\utils\ObbzYii;
use obbz\yii2\widgets\fileupload\behaviors\MultipleUploadDbBehavior;

/**
 * @var FlexibleModuleItem[] $relateItems
 * @var FlexibleModuleItem[] $relateFeItems
*/

class FlexibleModule extends \obbz\yii2\admin\models\base\FlexibleModuleBase
{
    const KEY_IMAGE_SLIDE = 'image_slide';
    const KEY_IMAGE_GALLERY = 'image_gallery';
    const KEY_YOUTUBE_GALLERY = 'youtube_gallery';

    const ITEM_REF_FIELD = 'flexible_module_id';

    const MARKER_CSS_CLASS = 'obbz-flexible-module';

    const SCENARIO_UPLOAD_IMAGE = "upload_image";

    const COL_1 = 'col-lg-12';
    const COL_2 = 'col-lg-6';
    const COL_3 = 'col-lg-4 col-sm-6';
    const COL_4 = 'col-lg-3 col-md-4';

    public $columnPatterns = [
        self::COL_1 => 'Full Width Column',
        self::COL_2 => '2 Columns',
        self::COL_3 => '3 Columns',
        self::COL_4 => '4 Columns',
    ];



    public $uploadItems;
    public $editorPath = '/editor/flexible-module';

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
			['image', 'image', 'extensions' => 'jpg, jpeg, webp',
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
                'itemRefField' => self::ITEM_REF_FIELD,
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

    /**
     * @return string RTE ifram for module
     */
    public function getRteMarker(){
        $result = '';
        if($this->id){

            $options = [
                'class' => self::MARKER_CSS_CLASS,
                'data-flexmodule-id'=>$this->id,
                'frameborder' => 0,
                'src' => \yii\helpers\Url::to([$this->editorPath . '/ck-view', 'id'=>$this->id], true)
            ];

            $result = Html::tag('iframe', '', $options);
        }

        return $result;
    }

    public static function getSubtituteMarkers($html){
        $pattern = '/<iframe.*class=.'. self::MARKER_CSS_CLASS .'.*<\/iframe>/';
        preg_match_all($pattern, $html, $matches);
        return isset($matches[0]) ? $matches[0] : [];
    }

    public static function getSubtituteModels($html){
        $pattern = '/data-flexmodule-id="([^"]*)"/';
        $iframes = self::getSubtituteMarkers($html);
        $ids = [];
        foreach($iframes as $iframe){
            preg_match($pattern, $iframe, $m);
            if(isset($m[1])){
                $ids[] = $m[1];
            }
        }
        $query = self::find()->andWhere(['id'=>$ids]);
        if(!empty($ids)){
            $query->orderBy(
                [new \yii\db\Expression('FIELD (id, ' . implode(',', $ids) . ')')]
            );
        }
        return $query->tAll();
    }

    public static function replaceSubtituteMarkers($html, $replaces){
        $patterns = self::getSubtituteMarkers($html);
        return str_replace($patterns, $replaces, $html);
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