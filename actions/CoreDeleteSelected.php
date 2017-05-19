<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\actions;


use obbz\yii2\models\CoreActiveRecord;
use obbz\yii2\utils\ObbzYii;
use yii\web\BadRequestHttpException;

class CoreDeleteSelected extends CoreSelected
{
    public $successText = "Record has been deleted successfully";
    public $errorText = "Can not delete this record";

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
                    $model->markDelete();
                }
                $transaction->commit();
                ObbzYii::setFlashSuccess(\Yii::t('obbz', $this->successText));
            } catch (\Exception $e) {
                $transaction->rollBack();
                ObbzYii::setFlashError(\Yii::t('obbz', $this->errorText));
            }
        }

        return $this->controller->redirect($this->redirectUrl);
    }

}