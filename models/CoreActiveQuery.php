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

    #region data list
    public function publishedList($showAttribute = 'title'){ // for fe
        // todo find by cache
        return ArrayHelper::map($this->published()->defaultOrder()->all(), 'id', $showAttribute);
    }

    public function activeList($showAttribute = 'title'){ // for be
        // todo find by cache
        return ArrayHelper::map($this->active()->defaultOrder()->all(), 'id', $showAttribute);
    }
    #endregion

}