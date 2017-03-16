<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\actions;


use obbz\yii2\models\CoreActiveRecord;
use obbz\yii2\utils\ObbzYii;
use yii\web\BadRequestHttpException;

class CoreUnpublishSelected extends CoreSelected
{
    public $successText = "Mark as unpublish successfully";
    public $errorText = "Can not mark as unpublish";

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
                    $model->markUnpublish();
                }
                $transaction->commit();
                ObbzYii::setFlashSuccess(\Yii::t('app', $this->successText));
            } catch (\Exception $e) {
                $transaction->rollBack();
                ObbzYii::setFlashError(\Yii::t('app', $this->errorText));
            }
        }

        return $this->controller->redirect($this->redirectUrl);
    }

}