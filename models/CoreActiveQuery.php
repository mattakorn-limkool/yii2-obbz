<?php
/**
 * Created by PhpStorm.
 * User: mattakorn
 * Date: 21/2/2560
 * Time: 4:51
 */

namespace obbz\yii2\models;

use Codeception\Lib\Interfaces\ActiveRecord;
use obbz\yii2\utils\Html;
use obbz\yii2\utils\ObbzYii;
use yii\base\Model;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class CoreActiveQuery extends ActiveQuery
{
    /**
     * for handle fail request when data is empty
     * @param $data
     * @throws NotFoundHttpException
     */
    public function handleFailRequest($data){
        if(empty($data)){
            throw new NotFoundHttpException(\Yii::t('app',"The requested page does not exist."));
        }
    }

    #region util todo - reslove this way issues https://github.com/yiisoft/yii2/issues/7263
    public function baseField($field){
        $modelClass = $this->modelClass;
        $t = $modelClass::tableName() . '.';
        return $t.$field;
    }
    #endregion

    #region util find

    /**
     * find request or fail if not found data
     * @return array|null|\yii\db\ActiveRecord
     */
    public function oneOrFail(){
        $data = $this->one();
        $this->handleFailRequest($data);
        return $data;
    }


    /**
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



    /**
     * find by primary key and returns a single row of result.
     * @param $db
     * @return array|null|\yii\db\ActiveRecord
     */
    public function pk($id){
        return $this->andWhere([$this->baseField('id') =>$id])->one();
    }

    /**
     * find request or fail if not found data
     * @param $db
     * @return array|null|\yii\db\ActiveRecord
     */
    public function pkOrFail($id){
        $data = $this->pk($id);
        $this->handleFailRequest($data);
        return $data;
    }

    /**
     * find by key_name and returns a single row of result.
     * @param $key
     * @return array|null|\yii\db\ActiveRecord
     */
    public function key($key){
        $modelClass = $this->modelClass;
        return $this->andWhere([$modelClass::tableName().'.key_name'=>$key])->one();
    }

    /**
     * find all by key_name
     * @param $key
     * @return array|\yii\db\ActiveRecord[]
     */
    public function keyAll($key){
        $modelClass = $this->modelClass;
        return $this->andWhere([$modelClass::tableName().'.key_name'=>$key])->all();
    }



    /**
     * find one with traslation content
     * @param null $language
     * @param null $db
     * @return array|null|\yii\db\ActiveRecord
     */
    public function translateOne($language = null, $db = null){
        if($language === null)
            $language = \Yii::$app->language;

        $doTranslate = \Yii::$app->params['language'] !=  $language;

        if($doTranslate){
            $modelClass = $this->modelClass;
            $t = $modelClass::tableName();
            $oriQuery = clone $this;
            $oriModel = $oriQuery->one($db);
            if($oriModel){
                $translateQuery = clone $this;
                $translateModel = $translateQuery->where(["{$t}.language"=>$language, "{$t}.language_pid"=>$oriModel->id])->one($db);
                return $modelClass::replaceTranslationWithoutQuery($oriModel, $translateModel);
            }else{
                return $oriModel;
            }
        }else{
            // todo- need to check why using all for query
//            return $this->all($db);
            return $this->one($db);
        }
    }

    /**
     * find all with traslation content
     * @param null $language
     * @param null $db
     * @return array|\yii\db\ActiveRecord[]
     */
    public function translateAll($language = null, $db = null){
        if($language === null)
            $language = \Yii::$app->language;

        $doTranslate = \Yii::$app->params['language'] !=  $language;

        if($doTranslate){
            $modelClass = $this->modelClass;
            $t = $modelClass::tableName();
            $oriQuery = clone $this;
            $oriModels = $oriQuery->all($db);
            $ids = \obbz\yii2\utils\ArrayHelper::prepareInQueryArray($oriModels, 'id');
            $translateQuery = clone $this;
            $translateModels = $translateQuery->where(["{$t}.language"=>$language, "{$t}.language_pid"=>$ids])->all($db);

            return $modelClass::replaceAllTranslationWithoutQuery($oriModels, $translateModels);
        }else{
            return $this->all($db);
        }
    }

    #endregion

    #region default scope

    public function whereKey($keyName, $field = 'key_name'){
        $modelClass = $this->modelClass;
        $t = $modelClass::tableName();
        $this->andWhere(["{$t}.{$field}"=>$keyName]);
        return $this;
    }

    /**
     * Show record on Frontend
     * @return $this
     */
    public function published(){
        $this->defaultLanguage();
        $modelClass = $this->modelClass;
//        $this->andWhere(['not','( disabled <> 0) OR ( deleted <> 0) ']);
        $this->andWhere(['<>',$modelClass::tableName().'.disabled',1]);
        $this->andWhere(['<>',$modelClass::tableName().'.deleted',1]);
        return $this;
    }

    /**
     * Hide record on Frontend
     * @return $this
     */
    public function unpublished(){
        $this->defaultLanguage();
        $modelClass = $this->modelClass;
        $this->andWhere('('.$modelClass::tableName().'.deleted = 1 OR '.$modelClass::tableName().'.disabled = 1)');
        return $this;
    }

    /**
     * Show record on Backend
     * @return $this
     */
    public function active(){
        $this->defaultLanguage();
        $modelClass = $this->modelClass;
        $this->andWhere([$modelClass::tableName().'.deleted'=>0]);
        return $this;
    }

    /**
     * Hide record on Backend
     * @return $this
     */
    public function archived(){
        $this->defaultLanguage();
        $modelClass = $this->modelClass;
        $this->andWhere([$modelClass::tableName().'.deleted'=> 1]);
        return $this;
    }

    /**
     * default order via sorting
     * @return $this
     */
    public function defaultOrder(){
        $modelClass = $this->modelClass;
        $this->orderBy([$modelClass::tableName().'.sorting'=>SORT_ASC]);
        return $this;
    }

    /**
     * filter by current user
     * @return $this\
     */
    public function onlyMe(){
        $modelClass = $this->modelClass;
        $this->andWhere([$modelClass::tableName().'.create_user_id'=>ObbzYii::user()->id]);
        return $this;
    }

    /** filter by default language ( null value is default language)
     * @return $this
     */
    public function defaultLanguage(){
        $modelClass = $this->modelClass;

        if($modelClass::supportedTranslationTable($modelClass)){
            $this->andWhere([$modelClass::tableName().'.language'=>null]);
        }
        return $this;
    }

    /**
     * filter by start_time and end_time
     * @param string $startTimeField
     * @param string $endTimeField
     * @return $this
     */
    public function withPeriodTime($startTimeField = 'start_time', $endTimeField = 'end_time'){
        $curTime = ObbzYii::formatter()->asDbDatetime();
        $modelClass = $this->modelClass;
        $t = $modelClass::tableName();
        $this->andWhere("IF({$t}.{$startTimeField} IS NULL, 1, {$t}.{$startTimeField} <= '$curTime')");
        $this->andWhere("IF({$t}.{$endTimeField} IS NULL, 1, {$t}.{$endTimeField} >= '$curTime')");
        return $this;
    }

    #endregion

    #region find with cache
    /**
     * find published all via cache
     * @param bool|true $cache
     * @return array|mixed|\yii\db\ActiveRecord[]
     */
    public function publishedAll($cache = true){
        $query = $this->published()->defaultOrder();
        $modelClass = $this->modelClass;
        if($cache){
            $key = $this->getCacheKey($modelClass::CACHE_PUBLISHED_ALL);
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

    /**
     * find published first single row
     * @param bool|true $cache
     * @return null|\yii\db\ActiveRecord
     */
    public function publishedFirst($cache = true){
        $data = $this->publishedAll($cache);
        return !empty($data) ? $data[0] : null;
    }

    /**
     * find published single row by pk
     * @param $id
     * @param bool|true $cache
     * @return null|\yii\db\ActiveRecord
     */
    public function publishedPk($id, $cache = true){
        $items = $this->publishedAll($cache);
        foreach($items as $item){
            if($item->id == $id){
                return $item;
            }
        }
        return null;
    }

    /**
     * find all of active rows
     * @param bool|true $cache
     * @return array|mixed|\yii\db\ActiveRecord[]
     */
    public function activeAll($cache = true){
        $modelClass = $this->modelClass;
        if($cache){

            $key = $this->getCacheKey($modelClass::CACHE_ACTIVE_ALL);
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

    /**
     * find active single row by pk
     * @param $id
     * @param bool|true $cache
     * @return null|\yii\db\ActiveRecord
     */
    public function activePk($id, $cache = true){
        $items = $this->activeAll($cache);
        foreach($items as $item){
            if($item->id == $id){
                return $item;
            }
        }
        return null;
    }

    /**
     * find active first row
     * @param bool|true $cache
     * @return null|\yii\db\ActiveRecord
     */
    public function activeFirst($cache = true){
        $data = $this->activeAll($cache);
        return !empty($data) ? $data[0] : null;
    }

    /**
     * using cache by query and cache key
     * @param $query
     * @param $key
     * @param null $duration
     * @param null $dependency
     * @return \yii\db\ActiveRecord[]
     */
    public function byCache($query, $key, $duration = null, $dependency = null){
        $data = ObbzYii::cache()->get($key);
        if($data === false){
            $data =  $query->all();
            ObbzYii::cache()->set($key, $data, $duration, $dependency);
            return $data;
        }
        return $data;
    }

    /**
     * get cache key with prefix by model
     * @param $key
     * @return string
     */
    public function getCacheKey($key){
        $modelClass = $this->modelClass;
        return $modelClass::CACHE_PREFIX . $key;
    }
    #endregion

    #region data list
    /**
     * find all list by dynamic query
     * @param string $showAttribute
     * @param string $pk
     * @return array
     */
    public function allList( $showAttribute = 'title', $pk = 'id'){
        $modelClass = $this->modelClass;
//        $t = $modelClass::tableName();
        $data = $this->all();
        return ArrayHelper::map($data, $pk, $showAttribute);
    }

    /**
     * find all list by dynamic query with cache
     * @param $cacheKey
     * @param string $showAttribute
     * @param string $pk
     * @return array
     */
    public function allListByCache($cacheKey, $showAttribute = 'title', $pk = 'id'){
        $data = $this->byCache($this, $cacheKey);
        return ArrayHelper::map($data, $pk, $showAttribute);
    }

    /**
     * find published list for all of model
     * support basic model only
     * @param string $showAttribute
     * @param bool|true $cache
     * @param string $pk
     * @return array
     */
    public function publishedList($showAttribute = 'title', $cache  = true, $pk = 'id'){ // for fe
        return ArrayHelper::map($this->publishedAll($cache), $pk, $showAttribute);
    }

    /**
     * find active list for all of model
     * support basic model only
     * @param string $showAttribute
     * @param bool|true $cache
     * @param string $pk
     * @return array
     */
    public function activeList($showAttribute = 'title', $cache  = true, $pk = 'id'){ // for be
        return ArrayHelper::map($this->activeAll($cache), $pk, $showAttribute);
    }

    #endregion







}