<?php
namespace obbz\yii2\models;
use obbz\yii2\utils\ObbzYii;
use yii\db\ActiveQuery;

/**
 * HOW TO replace yii active query with
 * Write down in your application bootstrap code:
 * Yii::$container->set('yii\db\ActiveQuery', 'obbz\yii2\models\EnhancedActiveQuery');
 *
 *
 * Class EnhancedActiveQuery
 * @package obbz\yii2\models
 */
class EnhancedActiveQuery extends ActiveQuery{

    private $whereAttributes = [];

    /**
     * @deprecated move to CoreActiveQuery
     * find one if not found create new model
     * @param null $initModelConfig
     * @param Connection|null $db the DB connection used to create the DB command.
     * @return ActiveRecord
     */
    public function oneOrCreate($initModelConfig = null, $replaceCondition = true, $db = null){
        $modelClass = $this->modelClass;
//        ObbzYii::debug($this);
        if($model = $this->one($db)){
            return $model;
        }else{
            $model = new $modelClass($initModelConfig);
            if(isset($this->where) && $replaceCondition){
                $this->whereAttributes = [];
                $this->whereToAttributes($this->where);
                $model->attributes = $this->whereAttributes;
            }
            return $model;
        }
    }


    /**
     * @deprecated move to CoreActiveQuery
     * auto grap filter condition to set attributes value of new model
     * @param $where
     */
    private function whereToAttributes($where){
        if(isset($where[0])){
            $operator = array_shift($where);
            foreach($where as $cond){
                if(is_array($cond)){
                    $this->whereToAttributes($cond);
                }else{
                    $this->whereAttributes[$where[0]] = $where[1];
                }
            }

        }else{
            foreach($where as $key=>$val){
                $this->whereAttributes[$key] = $val;
            }

        }

    }

}