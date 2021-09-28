<?php

namespace obbz\yii2\actions;

use obbz\yii2\models\CoreActiveRecord;
use yii\web\BadRequestHttpException;

class CoreSorting extends CoreBaseAction
{
    public $modelClass;
    public $orderAttribute = 'sorting';
//    public $scenario = CoreActiveRecord::SCENARIO_BE_UPDATE;

    public function run()
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            foreach (\Yii::$app->request->post('sorting') as $order => $id) {
                /** @var CoreActiveRecord $model */
                $model =  $this->findModel($id);
//                $model->setScenario($this->scenario);
                if ($model === null) {
                    throw new BadRequestHttpException();
                }
                $model->{$this->orderAttribute} = $order;
                $model->save(false, [$this->orderAttribute]);
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
        }
    }

}