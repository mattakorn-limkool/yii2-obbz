<?php
namespace obbz\yii2\admin\controllers;
use obbz\yii2\admin\models\FlexibleModule;
use obbz\yii2\controllers\CoreController;
use obbz\yii2\utils\ObbzYii;

use obbz\yii2\widgets\fileupload\actions\DeleteFileDbAction;
use obbz\yii2\widgets\fileupload\actions\MultipleUploadDbAction;
use yii\helpers\ArrayHelper;

use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * @author  Mattakorn Limkool <obbz.dev@gmail.com>
 *
 */
class FlexibleModuleController extends CoreController
{
    public $layout = '//blank';
    public $modelClass = FlexibleModule::class;
    public $scenarios = [
        'create' => FlexibleModule::SCENARIO_BE_CREATE,
        'update' => FlexibleModule::SCENARIO_BE_UPDATE,
        'uploadImage' => FlexibleModule::SCENARIO_UPLOAD_IMAGE,
    ];

//    public function init(){
//        parent::init();
//
//    }

    public function actions()
    {
        $modelClass = $this->modelClass;
        $modelObj = new $modelClass;
        $parent = parent::actions();

        return array_merge($parent, [
            'image-upload' => [
                'class'=>MultipleUploadDbAction::class,
                'modelClass'=>$modelClass,
//                'deleteUrl'=>['image-delete'],
                'scenario'=>$this->scenarios['uploadImage'],
            ],
            'image-delete' => [
                'class'=> DeleteFileDbAction::class,
                'modelItemClass'=>$modelObj->getUploadItemModel(),
                'itemRefField'=>$modelClass::ITEM_REF_FIELD
            ],
        ]);
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionCreate($section=null,$custom1=null, $custom2=null, $custom3=null, $custom4=null,$custom5=null){

        /** @var FlexibleModule $model */
        $model = new $this->modelClass;
        $model->section = $section;
        $model->custom_1 = $custom1;
        $model->custom_2 = $custom2;
        $model->custom_3 = $custom3;
        $model->custom_4 = $custom4;
        $model->custom_5 = $custom5;
        $model->setScenario($this->scenarios['create']);

        if($model->load(ObbzYii::post())){
            if($model->save()){
                return $this->getReturnRte($model, 'insert');
            }else{
                ObbzYii::setFlashError($model->getFirstErrors());
                return $this->refresh();
            }
//            ObbzYii::setFlashSuccess("Saved");
//            return $this->redirect(['success']);
        }

        return $this->render('create', compact('model'));
    }

    public function actionUpdate($id){
        $model = $this->findModel($id);
        $model->setScenario($this->scenarios['update']);

        if($model->load(ObbzYii::post())){
            if($model->save()){
               return $this->getReturnRte($model, 'update');
            }else{
                ObbzYii::setFlashError($model->getFirstErrors());
                return $this->refresh();
            }
//            ObbzYii::setFlashSuccess("Saved");
//            return $this->redirect(['success']);
        }

        return $this->render('update', compact('model'));
    }


    public function actionCkView($id){
        $model = $this->findModel($id);
        return $this->render('ck-view', compact('model'));
    }

    public function getReturnRte($model, $action){
        $insertHtml = $model->getRteMarker();
        $modelId = $model->id;
        return "<script type='text/javascript'>
                var dialog = window.parent.CKEDITOR.dialog.getCurrent();
                dialog.definition.onDataSuccess('$insertHtml', '$action', '$modelId');
                </script>";
    }


    /**
     * @param $id
     * @return FlexibleModule
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        $modelClass = $this->modelClass;

        if (($model = $modelClass::find()->active()->pk($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
//
//    public function actionSuccess(){
//        $insertHtml = 'test';
//        return "<script type='text/javascript'>
//                var dialog = window.parent.CKEDITOR.dialog.getCurrent();
////                console.log(dialog);
//                dialog.definition.onDataSuccess('$insertHtml');
//                </script>";
//    }





}