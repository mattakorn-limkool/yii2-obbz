<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\behaviors;


use creocoder\translateable\TranslateableBehavior;
use obbz\yii2\utils\ObbzYii;
use yii\base\Model;
use yii\db\ActiveRecord;

class TranslationBehavior extends TranslateableBehavior
{
//    public function afterValidate()
//    {
//        // todo - check below when update model
//        if (!Model::validateMultiple($this->owner->{$this->translationRelation})) {
//            $this->owner->addError($this->translationRelation);
//        }
//    }

    /**
     * Shortcut easy using for showing data
     * Returns default the translation owner model for default language.
     * be carefully if call this method that will be replace all attribute to owner model
     * @return ActiveRecord
     */
    public function replaceTranslation($language = null){

        if(!isset($language)){
            $language = \Yii::$app->language;
            $doTranslate = \Yii::$app->params['language'] !=  $language;
        }
        else // force to translate
            $doTranslate = true;



        if($doTranslate){
            $translate = $this->translate($language);
            foreach($this->translationAttributes as $attribute){
                if($translate->$attribute !== null and $translate->$attribute !== ''){
                    $this->owner->$attribute = $translate->$attribute;
                }
            }
        }
        return $this->owner;
    }

    /**
     * save translate for core model support language + language_pid only
     * @param $language
     * @param $languagePid
     */
    public function saveTranslate($language, $languagePid){
        $this->owner->language = $language;
        $this->owner->language_pid = $languagePid;
        return $this->owner->save();
    }

    public function replaceOriginWhenEmpty($originModel){
        foreach($this->translationAttributes as $attribute){

            if(empty($this->owner->$attribute)){
                $this->owner->$attribute = $originModel->$attribute;
            }
        }
    }




}