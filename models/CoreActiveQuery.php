<?php
/**
 * Created by PhpStorm.
 * User: mattakorn
 * Date: 21/2/2560
 * Time: 4:51
 */

namespace obbz\yii2\models;

use Codeception\Lib\Interfaces\ActiveRecord;
use obbz\yii2\utils\ObbzYii;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

class CoreActiveQuery extends ActiveQuery
{
    #region util find
    /**
     * need to find by pk
     * @param $id
     * @return array|null|\yii\db\ActiveRecord
     */
    public function pk($id){
        return $this->andWhere(['id'=>$id])->one();
    }
    public function key($key){
        return $this->andWhere(['key_name'=>$key])->one();
    }
    public function keyAll($key){
        return $this->andWhere(['key_name'=>$key])->all();
    }
    #endregion

    #region default scope
    /**
     * Show record on Frontend
     * @return $this
     */
    public function published(){
        $modelClass = $this->modelClass;
//        $this->andWhere(['not','( disabled <> 0) OR ( deleted <> 0) ']);
        $this->andWhere(['<>',$modelClass::tableName().'.disabled',1]);
        $this->andWhere(['<>',$modelClass::tableName().'.deleted',1]);
        return $this;
    }

    /**
     * Show record on Frontend
     * @return $this
     */
    public function unpublished(){
        $modelClass = $this->modelClass;
        $this->andWhere('('.$modelClass::tableName().'.deleted = 1 OR '.$modelClass::tableName().'.disabled = 1)');
        return $this;
    }

    /**
     * Show record on Backend
     * @return $this
     */
    public function active(){
        $modelClass = $this->modelClass;
        $this->andWhere([$modelClass::tableName().'.deleted'=>0]);
        return $this;
    }

    /**
     * Record has been set deleted
     * @return $this
     */
    public function archived(){
        $modelClass = $this->modelClass;
        $this->andWhere([$modelClass::tableName().'.deleted'=> 1]);
        return $this;
    }

    public function defaultOrder(){
        $modelClass = $this->modelClass;
        $this->orderBy([$modelClass::tableName().'.sorting'=>SORT_ASC]);
        return $this;
    }
    #endregion

    #region
    public function publishedAll($cache = true){
        $query = $this->published()->with('parent')->defaultOrder();
        $modelClass = $this->modelClass;
        if($cache){
            $key = $modelClass::CACHE_PUBLISHED_ALL;
            $data = ObbzYii::cache()->get($key);
            if($data === false){
                // flush cache when admin edit
                $data =  $query->all();
                ObbzYii::cache()->set($key, $data);
                return $data;
            }
            return $data;
        }else{
            return $query->all();
        }
    }
    public function publishedFirst($cache = true){
        $data = $this->publishedAll($cache);
        return !empty($data) ? $data[0] : null;
    }

    public function activeAll($cache = true){
        $modelClass = $this->modelClass;
        if($cache){
            $key = $modelClass::CACHE_ACTIVE_ALL;
            $data = ObbzYii::cache()->get($key);
            if($data === false){
                // flush cache when admin edit
                $data =  $this->active()->defaultOrder()->all();
                ObbzYii::cache()->set($key, $data);
                return $data;
            }
            return $data;
        }else{
            return $this->active()->defaultOrder()->all();
        }
    }
    public function activeFirst($cache = true){
        $data = $this->activeAll($cache);
        return !empty($data) ? $data[0] : null;
    }
    #endregion



    #region data list
    public function publishedList($showAttribute = 'title', $cache  = true){ // for fe
        return ArrayHelper::map($this->publishedAll($cache), 'id', $showAttribute);
    }

    public function activeList($showAttribute = 'title', $cache  = true){ // for be
        return ArrayHelper::map($this->activeAll($cache), 'id', $showAttribute);
    }
    #endregion

}