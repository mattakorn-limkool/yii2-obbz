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
            ObbzYii::setFlashSuccess(\Yii::t('obbz', $this->successText));
        }else{
            ObbzYii::setFlashError(\Yii::t('obbz', $this->errorText));
        }

        return $this->controller->redirect($this->redirectUrl);
    }

}