<?php

namespace obbz\yii2\models\query;
use \obbz\yii2\models\Rating;

/**
 * This is the ActiveQuery class for [[\obbz\yii2\models\Rating]].
 *
 * @see \obbz\yii2\models\Rating
 */
class RatingQuery extends \obbz\yii2\models\CoreActiveQuery
{

    /**
     * @inheritdoc
     * @return \obbz\yii2\models\Rating[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \obbz\yii2\models\Rating|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

//    public function published(){
//        $t = Rating::tableName();
//        return $this;
//    }

//    public function active(){
//        $t = Rating::tableName();
//        return $this;
//    }

//    public function defaultOrder(){
//        $t = Rating::tableName();
//        $this->orderBy([
//            "{$t}.id"=>SORT_ASC
//        ]);
//        return $this;
//    }

}
