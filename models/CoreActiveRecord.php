<?php
/**
 * Created by PhpStorm.
 * User: mattakorn
 * Date: 21/2/2560
 * Time: 19:50
 */

namespace obbz\yii2\models;

use obbz\yii2\behaviors\UploadBehavior;
use obbz\yii2\behaviors\UploadImageBehavior;
use obbz\yii2\utils\ObbzYii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;


/**
 * This is the core model class for default table
 * @property integer $id
 * @property string $title
 * @property string $detail
 * @property string $img
 * @property integer $sorting
 * @property integer $disabled
 * @property integer $deleted
 * @property string $created_time
 * @property string $modify_time
 * @property string $deleted_time
 * @property integer $create_user_id
 * @property integer $modify_user_id
 * @property integer $deleted_user_id
 * @property string $key_name
 */
class CoreActiveRecord extends \yii\db\ActiveRecord
{
    public $statusPublish;
    /**
     * Default Scenario
     */
    const SCENARIO_SEARCH = "search";
    const SCENARIO_CREATE = "create";
    const SCENARIO_UPDATE = "update";
    const SCENARIO_DELETE = "delete";
    const SCENARIO_BE_SEARCH = "be_search";
    const SCENARIO_BE_CREATE = "be_create";
    const SCENARIO_BE_UPDATE = "be_update";
    const SCENARIO_BE_DELETE = "be_delete";

    public function scenarioSearch(){
        return [self::SCENARIO_SEARCH, self::SCENARIO_BE_SEARCH];
    }
    public function scenarioCreate(){
        return [self::SCENARIO_CREATE, self::SCENARIO_BE_CREATE];
    }
    public function scenarioUpdate(){
        return [self::SCENARIO_UPDATE, self::SCENARIO_BE_UPDATE];
    }
    public function scenarioDelete(){
        return [self::SCENARIO_DELETE, self::SCENARIO_BE_DELETE];
    }
    public function isScenario($arrayScenario){
        return in_array($this->scenario, $arrayScenario);
    }

    public function init(){

        if(!isset($this->uploadFolder)){
            $this->uploadFolder = $this->tableName();
        }
        parent::init();
    }


    /**
     * default rules for core model
     * @return array
     */
    public function rules()
    {
        return [
//            [['disabled', 'deleted'], 'default', 'value' => 0],
//            [['sorting'], 'default', 'value' => 99999],
        ];
    }

    public function behaviors()
    {
        return [
            // for sortable grid
            'sortable' => [
                'class' => \kotchuprik\sortable\behaviors\Sortable::className(),
                'query' => self::find(),
                'orderAttribute'=>'sorting',
            ],


        ];
    }
    public function attributeLabels(){
        return [
            'statusPublish' => \Yii::t('app', 'Publish Status'),
        ];
    }

    public function beforeValidate() {
        if(parent::beforeValidate()) {
            #region core

            if($this->isScenario(
                array_merge(
                    $this->scenarioCreate(),
                    $this->scenarioUpdate()
                ))){

                $this->deleted = ObbzYii::getValue($this, 'deleted', 0);
                $this->disabled = ObbzYii::getValue($this, 'disabled', 0);
//                $this->sorting = ObbzYii::getValue($this, 'sorting', 99999);

                $userId = ObbzYii::user()->getId();
                if($this->isNewRecord){
                    $this->created_time = ObbzYii::formatter()->asDbDatetime();
                    if(!empty($userId))
                        $this->create_user_id = $userId;
                }else{
                    $this->modify_time = ObbzYii::formatter()->asDbDatetime();
                    if(!empty($userId))
                        $this->modify_user_id = $userId;
                }
            }

            #endregion
            return true;
        }else{
            return false;
        }
    }



    /**
     * auto set logging for user change record
     * @param bool $insert
     * @return bool
     */
//    public function beforeSave($insert)
//    {
//        if (parent::beforeSave($insert)) {
//            #region core
//            $userId = ObbzYii::user()->getId();
//            if($this->isNewRecord){
//                $this->created_time = ObbzYii::dateDb(null, 'datetime');
//                if(!empty($userId))
//                    $this->create_user_id = $userId;
//            }else{
//                $this->modify_time = ObbzYii::dateDb(null, 'datetime');
//                if(!empty($userId))
//                    $this->modify_user_id = $userId;
//            }
//            #endregion
//
//            return true;
//        } else {
//            return false;
//        }
//    }

    /**
     * Mark this record as publish
     * @return bool
     */
    public function markPublish(){
        $this->disabled = 0;
        return $this->save(false, ['disabled']);
    }

    /**
     * Mark this record as unpublish
     * @return bool
     */
    public function markUnpublish(){
        $this->disabled = 1;
        return $this->save(false, ['disabled']);
    }

    /**
     * Mark this record as deleted
     * @return bool
     */
    public function markDelete(){
        $userId = ObbzYii::user()->getId();

        $this->deleted = 1;
        $this->deleted_time = ObbzYii::formatter()->asDbDatetime();
        if(!empty($userId))
            $this->deleted_user_id = $userId;

        return $this->save(false, ['deleted', 'deleted_time', 'deleted_user_id']);
    }

    /**
     * Mark this record as active (not deleted)
     * @return bool
     */
    public function markActive(){
        $this->deleted = 0;
        return $this->save(false, ['deleted']);
    }

    /**
     * check record has published
     * @return bool
     */
    public function hasPublished(){
        return $this->disabled ? false: true;
    }

    /**
     * check record has unpublished
     * @return bool
     */
    public function hasUnpublished(){
        return !$this->hasPublished();
    }
    /**
     * check record has active
     * @return bool
     */
    public function hasActive(){
        return !$this->hasDeleted();
    }
    /**
     * check record has deleted
     * @return bool
     */
    public function hasDeleted(){
        return $this->deleted ? true: false;
    }

    public function prepareCoreAttributesFilter(){
        $this->disabled = $this->disabled === "" || !isset($this->disabled) ? "": (int)$this->disabled;
        $this->deleted = $this->deleted === "" || !isset($this->deleted) ? "": (int)$this->deleted;
    }

    /**
     * @param $model CoreActiveRecord
     * @return string
     */
    public function displayPublishStatus($showHtml = true){
        $list = CoreDataList::statusPublish();
        $label =  ArrayHelper::getValue($list, $this->disabled);
        $status = $this->hasPublished() ? \Yii::t('app', 'Published') : \Yii::t('app', 'Unpublished');
        return $showHtml ? Html::tag('span' , $label, ['class'=>'core-grid-status-' .  $status ]): $label;
    }

    public function getCoreAttributes(){
        return self::attributes();
    }

    public $uploadFolder;

    /**
     * @param $attribute
     * @param array $thumbs
     *                    [
     *                      'thumb1' => ['width' => 300, 'height' => 300],
     *                      'thumb2' => ['width' => 300, 'quality' => 100],
     *                    ]
     *
     * @param array $options
     *      [
     *          scenarios => [],
     *          placeholder => string,
     *          path => string,
     *          url => string
     *      ]
     * @return array
     */
    public function defaultImgBehavior($attribute, $thumbs = [], $options = []){

        if(!isset($options['placeholder'])){
            $placeholder = "";
        }else if($options['placeholder'] === 'default'){
            $placeholder = '@uploadPath/default/'. $this->uploadFolder .'/default.png';
        }else{
            $placeholder = $options['placeholder'];
        }

        if(!isset($options['path']) or $options['path'] === 'default')
            $path = '@uploadPath/'. $this->uploadFolder .'/{id}';
        else
            $path = $options['path'];



        if(!isset($options['url']) or $options['url'] === 'default')
            $url = '@uploadUrl/'. $this->uploadFolder .'/{id}';
        else
            $url = $options['url'];


        return [
            'class' => UploadImageBehavior::className(),
            'attribute' => $attribute,
            'scenarios' => isset($options['scenarios']) ? $options['scenarios'] : [],
            'placeholder' => $placeholder,
            'path' => $path,
            'url' => $url,
            'thumbs' => $thumbs,

        ];
    }

    public function defaultFileBehavior(){
        // not implemented yet
    }

}

