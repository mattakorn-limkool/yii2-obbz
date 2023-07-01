<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\actions;

use obbz\yii2\utils\ObbzYii;
use yii\base\Action;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecordInterface;
use yii\web\NotFoundHttpException;

class CoreBaseAction extends Action
{
    /**
     * @var string class name of the model which will be handled by this action.
     */
    public $modelClass;
    public $findModel;
    public $redirectUrl = ['index'];
    public $successText = "";
    public $errorText = "";


    public function findModel($id)
    {
        if ($this->modelClass === null) {
            throw new InvalidConfigException(get_class($this) . '::$modelClass must be set.');
        }


        if ($this->findModel !== null) {
            return call_user_func($this->findModel, $id, $this);
        }

        /* @var $modelClass ActiveRecordInterface */
        $modelClass = $this->controller->getModelClass();
//        ObbzYii::debug($modelClass);
        $keys = $modelClass::primaryKey();
        if (count($keys) > 1) {
            $values = explode(',', $id);
            if (count($keys) === count($values)) {
                $model = $modelClass::findOne(array_combine($keys, $values));
            }
        } elseif ($id !== null) {
            $model = $modelClass::findOne($id);
        }

        if (isset($model)) {
            return $model;
        } else {
            throw new NotFoundHttpException("Object not found: $id");
        }
    }


}