<?php
/**
 * Created by PhpStorm.
 * User: mattakorn
 * Date: 21/2/2560
 * Time: 4:51
 */

namespace obbz\yii2\models;

use yii\db\ActiveQuery;

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
    #endregion

    #region default scope
    /**
     * Show record on Frontend
     * @return $this
     */
    public function published(){
//        $this->andWhere(['not','( disabled <> 0) OR ( deleted <> 0) ']);
        $this->andWhere(['<>','disabled',1]);
        $this->andWhere(['<>','deleted',1]);
        return $this;
    }

    /**
     * Show record on Frontend
     * @return $this
     */
    public function unpublished(){
        $this->andWhere('(deleted = 1 OR disabled = 1)');
        return $this;
    }

    /**
     * Show record on Backend
     * @return $this
     */
    public function active(){
        $this->andWhere(['deleted'=>0]);
        return $this;
    }

    /**
     * Record has been set deleted
     * @return $this
     */
    public function archived(){
        $this->andWhere(['deleted'=> 1]);
        return $this;
    }

    public function defaultOrder(){
        $this->orderBy(['sorting'=>SORT_ASC]);
        return $this;
    }
    #endregion

}