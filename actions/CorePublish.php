<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\actions;


use obbz\yii2\models\CoreActiveRecord;
use obbz\yii2\utils\ObbzYii;

class CorePublish extends CoreBaseAction
{
    public $successText = 'Mark as publish successfully';
    public $errorText = 'Can not mark as publish';

    public function run($id)
    {
        /** @var CoreActiveRecord $model */
        $model = $this->findModel($id);

        if($model->markPublish()){
            ObbzYii::setFlashSuccess(ObbzYii::t($this->successText));
        }else{
            ObbzYii::setFlashError(ObbzYii::t($this->errorText));
        }

        return $this->controller->redirect($this->redirectUrl);
    }

}