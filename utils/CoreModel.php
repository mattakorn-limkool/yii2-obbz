<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\utils;

use Codeception\Lib\Interfaces\ActiveRecord;
use Yii;


class CoreModel extends \yii\base\Model
{
    /**
     * Creates and populates a set of models.
     * @param $modelClass
     * @param array $multipleModels
     * @param array $scenario
     * @return \Codeception\Lib\Interfaces\ActiveRecord[]
     */
    public static function createDefaultMultiple($modelClass, $multipleModels = [], $scenario = [])
    {
        /** @var ActiveRecord[] $models */
        $models   = [];

        if(empty($multipleModels)){
            $options = [];
            if(isset($scenario) && isset($scenario['create'])){
                $options = ['scenario'=>$scenario['create']];
            }
            $models = [new $modelClass($options)];
        }else{
            $models = $multipleModels;
            if(isset($scenario) && isset($scenario['update'])){
                foreach($models as $model){
                    $model->setScenario($scenario['update']);
                }
            }
        }



        return $models;
    }

    /**
     * for update multiple model supported removing item
     * @param $modelClass
     * @param array $multipleModels
     * @param array $deletedIDs
     * @param array $scenario
     * @param string $idField
     * @return array
     */
    public static function loadDiffMultiple(
        $modelClass, $multipleModels = [],
        &$deletedIDs = [], $scenario = [], $idField= 'id'
    )
    {
        $oldIDs = ArrayHelper::map($multipleModels, $idField,$idField);

        $model    = new $modelClass;
        $formName = $model->formName();
        $post     = Yii::$app->request->post($formName);
        $models   = [];

        if (! empty($multipleModels)) {
            $keys = array_keys(ArrayHelper::map($multipleModels, $idField, $idField));
            $multipleModels = array_combine($keys, $multipleModels);
        }


        if ($post && is_array($post)) {
            foreach ($post as $i => $item) {
                if (isset($item[$idField]) && !empty($item[$idField]) && isset($multipleModels[$item[$idField]])) {
                    $models[$i] = $multipleModels[$item[$idField]];
                    if(isset($scenario) && isset($scenario['update'])){
                        $models[$i]->setScenario($scenario['update']);
                    }
                } else {
                    $models[$i] = new $modelClass;
                    if(isset($scenario) && isset($scenario['create'])){
                        $models[$i]->setScenario($scenario['create']);
                    }
                }
                $models[$i]->attributes = $item;

            }
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($post, $idField, $idField)));
        }

        unset($model, $formName, $post);

        return $models;
    }

    /**
     * @param $oldModels -
     *          [
     *              ['id'=>1, 'title'=>'A'],
     *              ['id'=>2, 'title'=>'B'],
     *          ]
     * @param $postModels
     *          [
     *              ['id'=>1, 'title'=>'A'],
     *              ['id'=>3, 'title'=>'C'],
     *          ]
     * @param string $idField  default is 'id'
     * @return array  ids to be remove
     *      [2]
     */
    public static function compareRemoveIdsByModel($oldModels, $postModels, $idField = 'id'){
        $oldIDs = ArrayHelper::map($oldModels, $idField,$idField);
        $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($postModels, $idField, $idField)));
        return $deletedIDs;
    }

    /**
     * @param $oldArray -
     *  ['1', '2']
     * @param $postArray -
     *  ['1', '3']
     * @return array ids to be remove
     *  ['3']
     */
    public static function compareRemoveIdsByArray($oldArray, $postArray){
        $deletedIDs = array_diff($oldArray, $postArray);
        return $deletedIDs;
    }

    /**
     * @deprecated
     * Creates and populates a set of models.
     *
     * @param string $modelClass
     * @param array $multipleModels
     * @return array
     */
    public static function createMultiple($modelClass, $multipleModels = [], $scenario = null)
    {
        $model    = new $modelClass;
        $formName = $model->formName();
        $post     = Yii::$app->request->post($formName);
        $models   = [];

        if (! empty($multipleModels)) {
            $keys = array_keys(ArrayHelper::map($multipleModels, 'id', 'id'));
            $multipleModels = array_combine($keys, $multipleModels);
        }


        if ($post && is_array($post)) {
            foreach ($post as $i => $item) {
                if (isset($item['id']) && !empty($item['id']) && isset($multipleModels[$item['id']])) {
                    $models[$i] = $multipleModels[$item['id']];
                } else {
                    $models[$i] = new $modelClass;
                }

//                if(isset($scenario)){
//                    $models[$i]->setScenario($scenario);
//                }
            }
        }

        unset($model, $formName, $post);

        return $models;
    }


}