<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\actions;


use obbz\yii2\models\CoreActiveRecord;
use obbz\yii2\utils\ObbzYii;

class CoreTranslate extends CoreBaseAction
{
    const INPUT_TYPE_TEXT = 'textInput';
    const INPUT_TYPE_TEXT_AREA = 'textarea';
    const INPUT_TYPE_RTE = 'cke';

    public $successText = 'Save translation successfully';
    public $errorText = 'Can not save translation, please try again';

    // optional
    public $view = '@vendor/obbz/yii2/actions/views/translate/form';
    public $layout = 'blank';
    public $translationAttributes = []; // for custom translation attributes via controller
    public $attributesOptions = [];

    private function initAttributesOptions($model){
        $attributes = $this->getTranslationAttributes($model);
        $defaultOptions = [];
        foreach($attributes as $attribute){
            if($attribute === 'title'){
                $defaultOptions[$attribute] = [
                    'type' => 'textInput',
                    'options' => ['maxlength' => true]
                ];
            }else{
                $defaultOptions[$attribute] = [
                    'type' => 'textarea',
                    'options' => ['rows'=> 3]
                ];
            }

        }
        $this->attributesOptions = array_merge($defaultOptions, $this->attributesOptions);
    }

    public function run($id, $language, $key= null)
    {

        $this->controller->layout = $this->layout;
        $message = '';
        $hasError = false;

        /** @var CoreActiveRecord $model */
        $model = $this->findModel($id);
        $translateModel = clone $model;
        $this->initAttributesOptions($model);

        if (ObbzYii::post()) {
            // set translate value to model
            foreach (ObbzYii::post(\yii\helpers\StringHelper::basename($this->modelClass), []) as $lang => $data) {
                foreach ($data as $attribute => $translation) {
                    $model->translate($lang)->$attribute = $translation;
                }
            }

            if($model->save()){
                $message = \Yii::t('app', $this->successText);
            }else{
                $message = \Yii::t('app', $this->errorText);
                $hasError = true;
            }
        }

        $translateModel->replaceTranslation($language);
        $translationAttributes = $this->getTranslationAttributes($model);

        return $this->controller->render($this->view,[
            'model' => $model,
            'translateModel' => $translateModel,
            'translationAttributes'=>$translationAttributes,
            'attributesOptions'=>$this->attributesOptions,
            'language'=>$language,
            'message'=>$message,
            'hasError'=>$hasError,
        ]);

    }

    /**
     * @param $model CoreActiveRecord
     * @return array
     */
    private function getTranslationAttributes($model){
        if(!empty($this->translationAttributes)){
            return $this->translationAttributes;
        }else{
            return ($model->behaviors()['translateable']['translationAttributes']) ?
                $model->behaviors()['translateable']['translationAttributes'] : []
                ;
        }

    }



}