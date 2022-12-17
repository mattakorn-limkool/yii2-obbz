<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\actions;


use obbz\yii2\models\ServerStatus;
use obbz\yii2\utils\ObbzYii;
use yii\base\Action;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;

/**
 * fore redirect and add touch counting for ads support behavior (AdsBehavior)
 * Class AdsTouchAction
 * @package obbz\yii2\actions
 */
class ServerStatusAction extends Action
{

    public $modelClass = ServerStatus::class;
    public $view = '@vendor/obbz/yii2/actions/views/server-status/view';

    public function run($cache = true)
    {
        if($this->modelClass == null){
            throw new InvalidConfigException('Please define $modelClass');
        }

        $model = new $this->modelClass();
        $model->prepareData($cache);

        return $this->controller->render($this->view,[
            'model' => $model,
        ]);
    }


}