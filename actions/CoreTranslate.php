<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\actions;


use obbz\yii2\models\CoreActiveRecord;
use obbz\yii2\utils\ObbzYii;
use obbz\yii2\utils\UploadedFile;

class CoreTranslate extends CoreBaseAction
{
    const INPUT_TYPE_TEXT = 'textInput';
    const INPUT_TYPE_TEXT_AREA = 'textarea';
    const INPUT_TYPE_RTE = 'cke';
    const INPUT_TYPE_IMAGE = 'image';
    const INPUT_TYPE_FILE = 'file';

    public $successText = 'Save translation successfully';
    public $errorText = 'Can not save translation, please try again';

    // optional
    public $view = '@vendor/obbz/yii2/actions/views/translate/form';
    public $layout = 'blank';
    public $translationAttributes = []; // for custom translation attributes via controller
    public $attributesOptions = [];
    public $scenarios = [
        'create' => 'translate_create',
        'update' => 'translate_update',
    ];

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

    public function run($id, $language)
    {
//        ObbzYii::debug($this->controller->modelClass);
        $this->controller->layout = $this->layout;
        $message = '';
        $hasError = false;

        /** @var CoreActiveRecord $model */
        $model = $this->findModel($id);
        $translateModel = $model->getTranslation($language);
        $this->initAttributesOptions($translateModel);
//        ObbzYii::debug($translateModel);
        if($translateModel->isNewRecord){
            $translateModel->setScenario($this->scenarios['create']);
        }else{
            $translateModel->setScenario($this->scenarios['update']);
        }
//        ObbzYii::debug($translateModel->scenario);

//        $translateModel = clone $model;


        if ($translateModel->load(ObbzYii::post())) {
//            ObbzYii::debug(UploadedFile::getInstance($translateModel, 'image'));
            // set translate value to model
//            foreach (ObbzYii::post(\yii\helpers\StringHelper::basename($this->modelClass), []) as $lang => $data) {
//                $langModel = $model->translate($lang);
//                $langModel->setScenario('translate_create');
//                foreach ($data as $attribute => $translation) {
////                    ObbzYii::debug(UploadedFile::getInstance($translateModel, '[en]image'));
//
//                    $langModel->$attribute = $translation;
////                    $model->translate($lang)->$attribute = $translation;
//                }
//            }



            if($translateModel->saveTranslate($language, $id)){
                $message = \Yii::t('obbz', $this->successText);
            }else{
                $message = \Yii::t('obbz', $this->errorText);
                $hasError = true;
            }
        }

        $translateModel->replaceOriginWhenEmpty($model);
//        ObbzYii::debug($model);
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