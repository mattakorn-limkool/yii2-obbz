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
    public $successText = 'Your comment has been added.';
    public $flashOnSuccess = true;

    public function run($id)
    {
        if($this->modelClass == null){
            throw new InvalidConfigException('Please define $modelClass');
        }
        $this->modelId = $id;

        $model = $this->newModel();

        if($model->load(ObbzYii::post())){
            //no authen use controller access control instead

            $model->key_name = $model::getSectionKey();
            $model->ip_address = ObbzYii::getIpAddress();
            if($model->save()){
                if($this->flashOnSuccess){
                    ObbzYii::setFlashSuccess(\Yii::t('obbz',$this->successText));
                }
            }else{
                ObbzYii::setFlashError($model->getFirstErrors());
            }
            $this->controller->redirect(ObbzYii::referrerUrl());
        }

//        return $this->controller->redirect($this->redirectUrl);
    }

    /**
     * @return Comment
     */
    protected function newModel(){
        /** @var Comment $model */
        $model = new $this->modelClass;
        $model->setScenario($model::SCENARIO_CREATE);
        $model->model_id = $this->modelId;
        return $model;
    }

}