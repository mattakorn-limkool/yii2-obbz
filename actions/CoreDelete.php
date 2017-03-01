<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\actions;


use obbz\yii2\models\CoreActiveRecord;
use obbz\yii2\utils\ObbzYii;

class CoreDelete extends CoreBaseAction
{
    public $successText = 'Record has been deleted successfully';
    public $errorText = 'Can not delete this record';

    public function run($id)
    {
        /** @var CoreActiveRecord $model */
        $model = $this->findModel($id);

        if($model->markDelete()){
            ObbzYii::setFlashSuccess(ObbzYii::t($this->successText));
        }else{
            ObbzYii::setFlashError(ObbzYii::t($this->errorText));
        }

        return $this->controller->redirect($this->redirectUrl);
    }

}