<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\actions;


use obbz\yii2\models\CoreActiveRecord;
use obbz\yii2\utils\ObbzYii;
use yii\web\BadRequestHttpException;

class CorePublishSelected extends CoreSelected
{
    public $successText = "Mark as publish successfully";
    public $errorText = "Can not mark as publish";

    public function run()
    {
        if($this->validate()) {
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                foreach (ObbzYii::post('selection') as $id) {
                    /** @var CoreActiveRecord $model */
                    $model = $this->findModel($id);
                    if ($model === null) {
                        throw new BadRequestHttpException();
                    }
                    $model->markPublish();
                }
                $transaction->commit();
                ObbzYii::setFlashSuccess(ObbzYii::t($this->successText));
            } catch (\Exception $e) {
                $transaction->rollBack();
                ObbzYii::setFlashError(ObbzYii::t($this->errorText));
            }
        }

        return $this->controller->redirect($this->redirectUrl);
    }

}