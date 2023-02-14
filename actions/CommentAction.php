<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\actions;


use common\models\Comment;
use obbz\yii2\models\CoreActiveRecord;
use obbz\yii2\utils\ObbzYii;
use yii\base\Action;
use yii\base\InvalidConfigException;

class CommentAction extends Action
{
    public $modelClass;
    public $modelId;
    public $scenario;
    public $successText = 'Your comment has been added.';
    public $flashOnSuccess = true;
    public $flashOnError = true;
    public $redirectUrl;

    public function run($id)
    {
        if($this->modelClass == null){
            throw new InvalidConfigException('Please define $modelClass');
        }
        $this->modelId = $id;

        $modelClass = $this->modelClass;
        $model = $modelClass::newModel($this->modelId);
//        $model = $this->newModel();
        if($this->scenario){
            $model->setScenario($this->scenario);
        }

        if($model->load(ObbzYii::post())){
            //no authen use controller access control instead

            $model->key_name = $model::getSectionKey();
            $model->ip_address = ObbzYii::getIpAddress();
            $this->beforeSave($model);
            if($model->save()){
                if($this->flashOnSuccess){
                    ObbzYii::setFlashSuccess(\Yii::t('obbz',$this->successText));
                }
            }else{
                if($this->flashOnError){
                    ObbzYii::setFlashError($model->getFirstErrors());
                }

            }
            $this->afterSave($model);
            $redirectUrl = isset($this->redirectUrl) ? $this->redirectUrl : ObbzYii::getReturnUrl();
            $this->controller->redirect($redirectUrl);
        }

//        return $this->controller->redirect($this->redirectUrl);
    }

    public function beforeSave($model){
    }

    public function afterSave($model){
    }



}