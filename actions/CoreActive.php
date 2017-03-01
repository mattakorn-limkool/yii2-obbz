<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\actions;


use obbz\yii2\models\CoreActiveRecord;
use obbz\yii2\utils\ObbzYii;

class CoreActive extends CoreBaseAction
{
    public $successText = 'Record has been active successfully';
    public $errorText = 'Can not active this record';

    public function run($id)
    {
        /** @var CoreActiveRecord $model */
        $model = $this->findModel($id);

        if($model->markActive()){
            ObbzYii::setFlashSuccess(ObbzYii::t($this->successText));
        }else{
            ObbzYii::setFlashError(ObbzYii::t($this->errorText));
        }

        return $this->controller->redirect($this->redirectUrl);
    }

}