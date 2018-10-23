<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\actions;

use obbz\yii2\models\Rating;
use obbz\yii2\utils\ObbzYii;
use yii\base\Action;
use yii\web\MethodNotAllowedHttpException;
use yii\web\Response;


class RatingAction extends Action
{


    public function run($entity, $target_id, $action, $version, $value)
    {
        if (!\Yii::$app->request->getIsAjax() || !\Yii::$app->request->getIsPost()) {
            $this->wrongMethod();
        }
        $user_id = ObbzYii::user()->id;
        if(empty($user_id)){ // not allow guest user
            return \Yii::$app->getResponse()->redirect(\Yii::$app->user->loginUrl);
        }

        \Yii::$app->response->format = Response::FORMAT_JSON;
        /** @var Rating $model */
        $model = Rating::find()->where([
            'entity'=>$entity,
            'target_id'=>$target_id,
            'user_id' => $user_id,
            'version' => $version
        ])->one();
        if($model){
            $model->value = $value;
        }else{
            $model = new Rating();
            $model->entity = $entity;
            $model->target_id = $target_id;
            $model->user_id = $user_id;
            $model->version = $version;
        }

        if($action == "rating:change"){
            $model->value = $value;
            if($model->save()){
                return ['result'=>'success'];
            }else{
                return $model->getFirstErrors();
            }

        }else if($action == "rating:clear" || $action == "rating:reset") {
            if($model->isNewRecord){
                $this->wrongMethod();
            }else{
                $model->delete();
            }
            return ['result'=>'success'];
        }else{
            $this->wrongMethod();
        }

//        $form = new Rating();
//        if($form->load(ObbzYii::post())){
//
//        }else{
//            $this->wrongMethod();
//        }
    }


    public function wrongMethod(){
        throw new MethodNotAllowedHttpException(\Yii::t('vote', 'Forbidden method'), 405);
    }

}