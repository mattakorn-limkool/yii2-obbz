<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\actions;


use obbz\yii2\models\CoreActiveRecord;
use obbz\yii2\utils\ObbzYii;

class CoreUnpublish extends CoreBaseAction
{
    public $successText = "Mark as unpublish successfully";
    public $errorText = "Can not mark as unpublish";

    public function run($id)
    {
        /** @var CoreActiveRecord $model */
        $model = $this->findModel($id);

        if($model->markUnpublish()){
            ObbzYii::setFlashSuccess(ObbzYii::t($this->successText));
        }else{
            ObbzYii::setFlashError(ObbzYii::t($this->errorText));
        }

        return $this->controller->redirect($this->redirectUrl);
    }

}