<?php
namespace obbz\yii2\admin\controllers;
use obbz\yii2\admin\models\TranslateForm;
use obbz\yii2\admin\models\TranslateModel;
use obbz\yii2\components\i18n\PhpMessageSource;
use obbz\yii2\utils\ObbzYii;
use yii\helpers\ArrayHelper;
use yii\i18n\I18N;

use yii\web\Controller;

/**
 * @author  Mattakorn Limkool <obbz.dev@gmail.com>
 *
 */
class TranslateController extends Controller
{
    public $translateLanguages;
//    public $translateDefaultLanguage;


    /**
     * @var array
     */
    public $keyMap = [];


//    public function init(){
//        parent::init();
//
//    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionUpdate($key, $lang = null)
    {
        $model = new TranslateForm();
//        $model->translateLanguages = $this->translateLanguages;
//        $model->translateDefaultLanguage = $this->translateDefaultLanguage;
        $model->prepareData($key, $lang, $this->keyMap, $this->translateLanguages);
//

        if(ObbzYii::post() && TranslateModel::loadMultiple($model->messageModels, ObbzYii::post())){
            $valid = false;
            if(TranslateModel::validateMultiple($model->messageModels)){
                if($model->saveTranslation()){
                    $valid = true;
                    ObbzYii::setFlashSuccess('Translated message has been saved.');
                }
            }

            if($valid == false){
                ObbzYii::setFlashError('Can not save data');
            }

            return $this->redirect(['update', 'key'=>$key, 'lang'=>$lang]);
//            ObbzYii::debug($model->messageModels);
        }

//
        return $this->render('update', [
            'model' => $model,

        ]);
    }

}