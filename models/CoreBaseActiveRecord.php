<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\models;


use obbz\yii2\behaviors\UploadBehavior;
use obbz\yii2\behaviors\UploadImageBehavior;

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

    public $uploadFolder;

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

//    public function rules()
//    {
//        return [
////            [['disabled', 'deleted'], 'default', 'value' => 0],
////            [['sorting'], 'default', 'value' => 99999],
//        ];
//    }

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