<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\models;


use obbz\yii2\behaviors\UploadBehavior;
use obbz\yii2\behaviors\UploadImageBehavior;
use obbz\yii2\utils\ObbzYii;
use yii\base\ErrorException;
use yii\helpers\ArrayHelper;

class CoreBaseActiveRecord extends \yii\db\ActiveRecord
{
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
    const SCENARIO_TRANSLATE_CREATE = "translate_create";
    const SCENARIO_TRANSLATE_UPDATE = "translate_update";

    const AUTODATE_TYPE_DATE = 'date';
    const AUTODATE_TYPE_DATETIME = 'datetime';
    const AUTODATE_TYPE_TIME = 'time';

    const AUTODATE_DBTYPE_DATE = 'date';
    const AUTODATE_DBTYPE_DATETIME = 'datetime';

    const CACHE_PREFIX = '';
    const CACHE_PREFIX_API = 'api-';

    public $uploadFolder;

    /**
     * Returns the fully qualified name of this class.
     * @return string the fully qualified name of this class.
     */
    public static function calledClass()
    {
        return get_called_class();
    }

    /**
     * @var array
     * eg.  [
     *              [
     *                 'field' => 'start_date',
     *                 'inputType' => 'date',
     *                 'dbType' => 'datetime',
     *              ]
     *           ]
     */
    public $autoDateFields = [];

    public function scenarioSearch(){
        return [self::SCENARIO_SEARCH, self::SCENARIO_BE_SEARCH];
    }
    public function scenarioCreate(){
        return [self::SCENARIO_CREATE, self::SCENARIO_BE_CREATE];
    }
    public function scenarioUpdate(){
        return [self::SCENARIO_UPDATE, self::SCENARIO_BE_UPDATE];
    }
    public function scenarioCU(){
        return array_merge($this->scenarioCreate(), $this->scenarioUpdate());
    }
    public function scenarioDelete(){
        return [self::SCENARIO_DELETE, self::SCENARIO_BE_DELETE];
    }
    public function scenarioTranslate(){
        return [self::SCENARIO_TRANSLATE_CREATE, self::SCENARIO_TRANSLATE_UPDATE];
    }
    public function isScenario($arrayScenario){
        return in_array($this->scenario, $arrayScenario);
    }

    public function scenarios()
    {
        if(self::tableName() == "{{%core_active_record}}"){
            return parent::scenarios();
        }else{
            $attrs = $this->attributes();
            \obbz\yii2\utils\ArrayHelper::removeValue($attrs, 'id');
            return array_merge(parent::scenarios(), [
//                self::SCENARIO_SEARCH => $attrs,
//                self::SCENARIO_CREATE => $attrs,
//                self::SCENARIO_UPDATE => $attrs,
//                self::SCENARIO_DELETE => $attrs,
//                self::SCENARIO_BE_SEARCH => $attrs,
//                self::SCENARIO_BE_CREATE => $attrs,
//                self::SCENARIO_BE_UPDATE => $attrs,
//                self::SCENARIO_BE_DELETE => $attrs,
                self::SCENARIO_TRANSLATE_CREATE => $attrs,
                self::SCENARIO_TRANSLATE_UPDATE => $attrs,
            ]);
        }

    }

    public function init(){

        if(!isset($this->uploadFolder)){
            $this->uploadFolder = $this->tableName();
        }
        parent::init();
    }

    public static function getCacheKey($key){
        $className = self::class;
        return $className::CACHE_PREFIX . $key;
    }


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
            $options['placeholder'] = "";
        }else if($options['placeholder'] === 'default'){
            $options['placeholder'] = '@uploadPath/default/'. $this->uploadFolder .'/default.jpg';
        }
//        else{
//            $placeholder = $options['placeholder'];
//        }


        if(!isset($options['path']) or $options['path'] === 'default')
            $path = '@uploadPath/'. $this->uploadFolder .'/{id}';
        else
            $path = $options['path'];



        if(!isset($options['url']) or $options['url'] === 'default')
            $url = '@uploadUrl/'. $this->uploadFolder .'/{id}';
        else
            $url = $options['url'];

//        if($options)
        return array_merge([
            'class' => UploadImageBehavior::class,
            'attribute' => $attribute,
//            'scenarios' => isset($options['scenarios']) ? $options['scenarios'] : [],
//            'placeholder' => $placeholder,
            'path' => $path,
            'url' => $url,
            'thumbs' => $thumbs,

        ], $options);
    }

    public function defaultFileBehavior($attribute, $options = []){
        if(!isset($options['path']) or $options['path'] === 'default')
            $path = '@uploadPath/'. $this->uploadFolder .'/{id}';
        else
            $path = $options['path'];

        if(!isset($options['url']) or $options['url'] === 'default')
            $url = '@uploadUrl/'. $this->uploadFolder .'/{id}';
        else
            $url = $options['url'];

        return array_merge([
            'class' => UploadBehavior::class,
            'attribute' => $attribute,
            'scenarios' => isset($options['scenarios']) ? $options['scenarios'] : [],
            'path' => $path,
            'url' => $url,
        ],$options);
    }

//    public function getThumb(){
//
//    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->autoDate2db();

            return true;
        } else {
            return false;
        }
    }
    public function afterSave($insert, $changedAttributes){
        $this->autoDate2input();
        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * allow scenarios for using auto datefield to auto convert from date2input
     * @return array
     */
    public function autoDateScenarios(){
        return $this->scenarioCU();
    }

    /**
     * convert autodatefield to input
     * @throws ErrorException
     */
    public function autoDate2input(){
        foreach($this->autoDateFields as $fieldsConf){

            #region validate
            $field = ArrayHelper::getValue($fieldsConf, 'field');
            if(!isset($field)){
                throw new ErrorException('Must be set field of AutoDateFields items');
            }
            #endregion
            $type = ArrayHelper::getValue($fieldsConf, 'inputType', self::AUTODATE_TYPE_DATE);
            if(!empty($this->$field)){
                $scenarios = ArrayHelper::getValue($fieldsConf, 'scenarios', $this->autoDateScenarios());
//                ObbzYii::debug($scenarios);
                if($this->isScenario($scenarios)){
                    if($type == self::AUTODATE_TYPE_DATE){
                        $this->$field =  ObbzYii::formatter()->asDate($this->$field);
                    }else if($type == self::AUTODATE_TYPE_DATETIME){
                        $this->$field =  ObbzYii::formatter()->asDatetime($this->$field);
                    }else if($type == self::AUTODATE_TYPE_TIME){
                        $this->$field =  ObbzYii::formatter()->asTime($this->$field);
                    }
                }
            }

        }
    }

    public function autoDate2db(){
        foreach($this->autoDateFields as $fieldsConf){
            #region validate
//            $dbType = ArrayHelper::getValue($fieldsConf, 'dbType', self::AUTODATE_TYPE_DATE);
//            if(!isset($dbType)){
//                throw new ErrorException('Must be set dbType of AutoDateFields items');
//            }
            $field = ArrayHelper::getValue($fieldsConf, 'field');
//            ObbzYii::debug($this->$field);
            if(!isset($field)){
                throw new ErrorException('Must be set field of AutoDateFields items');
            }
            #endregion
            $type = ArrayHelper::getValue($fieldsConf, 'inputType', self::AUTODATE_TYPE_DATE);

            if(!empty($this->$field)){

                $scenarios = ArrayHelper::getValue($fieldsConf, 'scenarios', $this->autoDateScenarios());

                if($this->isScenario($scenarios)){
//                    ObbzYii::debug($this->$field);
                    if($type == self::AUTODATE_TYPE_DATE){
                        $this->$field =  ObbzYii::formatter()->asDbDate($this->$field);
                    }else if($type == self::AUTODATE_TYPE_DATETIME){
                        $this->$field =  ObbzYii::formatter()->asDbDatetime($this->$field);
                    }else if($type == self::AUTODATE_TYPE_TIME){
                        $this->$field =  ObbzYii::formatter()->asDbTime($this->$field);
                    }
//                    $this->$field =  ObbzYii::formatter()->asDbDatetime($this->$field);
//                    if($dbType == self::AUTODATE_DBTYPE_DATETIME && $type == self::AUTODATE_TYPE_DATETIME){
//                        $this->$field =  ObbzYii::formatter()->asDbDatetime($this->$field);
//                    }else{
//                        $this->$field =  ObbzYii::formatter()->asDbDate($this->$field);
//                    }
                }
            }

        }
    }

    public function clearDefaultCacheApi(){
//        $className = self::class;
        ObbzYii::cache()->delete(self::getCacheApiByKey($this::CACHE_PUBLISHED_ALL));
        ObbzYii::cache()->delete(self::getCacheApiByKey($this::CACHE_ACTIVE_ALL));
    }

    public function getCacheApiByKey($key){
//        $className = self::class;
        return $this::CACHE_PREFIX_API . $key;
    }

    public static function supportedTranslationTable($modelClass){
        $obj = new $modelClass;
        return $obj->hasAttribute('language') &&  $obj->hasAttribute('language_pid');
    }

    public static function replaceAllTranslationWithoutQuery(&$originalModels, $translationModels){
        if(empty($originalModels) || empty($translationModels)){
            return $originalModels;
        }
        else{
            foreach($originalModels as $originalModel){
                foreach($translationModels as $translationModel){
                    if($originalModel->id == $translationModel->language_pid){

                        $originalModel = self::replaceTranslationWithoutQuery($originalModel, $translationModel);

                        break;
                    }
                }

            }

            return $originalModels;
        }
    }

    public static function replaceTranslationWithoutQuery($model, $translationModel = null){
        if(isset($translationModel)){
            foreach($model->translationAttributes as $attribute){
                if($translationModel->$attribute !== null and $translationModel->$attribute !== ''){
                    $model->owner->$attribute = $translationModel->$attribute;
                }
            }
        }

        return $model;
    }




}