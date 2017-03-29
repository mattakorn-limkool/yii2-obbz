<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\behaviors;


use creocoder\translateable\TranslateableBehavior;
use yii\base\Model;
use yii\db\ActiveRecord;

class TranslationBehavior extends TranslateableBehavior
{
    public function afterValidate()
    {
        // todo - check below when update model
//        if (!Model::validateMultiple($this->owner->{$this->translationRelation})) {
//            $this->owner->addError($this->translationRelation);
//        }
    }

    /**
     * Shortcut easy using for showing data
     * Returns default the translation owner model for default language.
     * ** be carefully if call this method that will be replace all attribute to owner model
     * @return ActiveRecord
     */
    public function replaceTranslation($language = null){
        $translate = $this->translate($language);
        foreach($this->translationAttributes as $attribute){
            if($translate->$attribute !== null and $translate->$attribute !== ''){
                $this->owner->$attribute = $translate->$attribute;
            }
        }
    }

}