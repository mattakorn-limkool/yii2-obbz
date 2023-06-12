<?php
/**
 * @author  Mattakorn Limkool <obbz.dev@gmail.com>
 *
 */

namespace obbz\yii2\admin\models;


use obbz\yii2\utils\ArrayHelper;
use obbz\yii2\utils\ObbzYii;
use yii\base\Model;

class TranslateForm extends Model
{
    public $title;
    public $section;
    public $language;
    public $messageModels;
    public $keyMap;
    public $sectionName;
    public $fromLabel;
    public $toLabel;
    public $translateLanguages;

    private $_i18nObj;
    private $_msgSource;
    private $_languages;

    public function attributeLabels(){
        return array_merge(parent::attributeLabels(),[
            'language' => 'To Language',
        ]);
    }


    public function prepareData($section, $language, $keyMap, $translationLanguages){
        // get yii default component config
        $components = ObbzYii::app()->getComponents();
        $this->_i18nObj =  \Yii::createObject($components['i18n']);
        $this->_msgSource =  $this->_i18nObj->getMessageSource($section);

        $this->section = $section;
        $this->keyMap = $keyMap;
        $this->translateLanguages = $translationLanguages;


        if($this->translateLanguages && is_array($this->translateLanguages) && isset($this->translateLanguages[0])){
            $callable = ArrayHelper::getValue($this->translateLanguages, '0');
            $args = ArrayHelper::getValue($this->translateLanguages, '1', []);
            $this->_languages = call_user_func_array($callable, $args);
        }else{
            $this->_languages = ArrayHelper::getValue( \Yii::$app->params, 'languages', []);
            $this->_languages[\Yii::$app->params['defaultLanguage']];
        }

        if($language == null){
            $this->language = key($this->_languages);
        }else{
            $this->language = $language;
        }


        $this->messageModels = TranslateModel::getModels( $this->_msgSource->adminLoadMessages($section, $this->language));

        $languages = ObbzYii::app()->params['languages'];
        $this->fromLabel = ArrayHelper::getValue($languages, ObbzYii::app()->params['defaultLanguage']);
        $this->toLabel = ArrayHelper::getValue($languages, $this->language);

        $this->prepareTitle();

    }

    protected function prepareTitle(){
        $this->sectionName = ArrayHelper::getValue($this->keyMap, $this->section);
        if(!isset($this->sectionName)){
            $this->sectionName = join(' ', array_map('ucfirst', explode('/', $this->section)));
        }
        $this->title = 'Translate '.  $this->sectionName;
    }

    public function saveTranslation(){
        $messagesArray = [];
        foreach($this->messageModels as $model){
            $messagesArray[$model->defaultMessage] = $model->translateMessage;
        }
        return $this->_msgSource->adminSaveMessage($this->section, $this->language, $messagesArray);
    }

    public function listTranslationLanguages(){
        return $this->_languages;
    }



}