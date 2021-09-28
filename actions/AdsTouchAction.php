<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\actions;


use common\models\Comment;
use obbz\yii2\models\CoreActiveRecord;
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
class AdsTouchAction extends Action
{
    /**
     * @var
     * eg. [
     *          'banner' => Banner::class,
     *          'post' => Post::class
     *     ]
     */
    public $modelMap;

    public function run($url, $model, $id)
    {
        if($this->modelMap == null){
            throw new InvalidConfigException('Please define $modelMap');
        }


        $modelClass = ArrayHelper::getValue($this->modelMap, $model);
        if(empty($modelClass)){
            throw new BadRequestHttpException();
        }

        $model = $modelClass::findOne($id);

        if (!isset($model)) {
            throw new NotFoundHttpException("Object not found: $id");
        }

        $model->addTouchCount();
        $url = urldecode($url);
        return $this->controller->redirect($url);
    }


}