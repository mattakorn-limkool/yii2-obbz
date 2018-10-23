<?php

namespace obbz\yii2\models\query;
use \obbz\yii2\models\RatingAggregate;

/**
 * This is the ActiveQuery class for [[\obbz\yii2\models\RatingAggregate]].
 *
 * @see \obbz\yii2\models\RatingAggregate
 */
class RatingAggregateQuery extends \obbz\yii2\models\CoreActiveQuery
{

    /**
     * @inheritdoc
     * @return \obbz\yii2\models\RatingAggregate[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \obbz\yii2\models\RatingAggregate|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

//    public function published(){
//        $t = RatingAggregate::tableName();
//        return $this;
//    }

//    public function active(){
//        $t = RatingAggregate::tableName();
//        return $this;
//    }

//    public function defaultOrder(){
//        $t = RatingAggregate::tableName();
//        $this->orderBy([
//            "{$t}.id"=>SORT_ASC
//        ]);
//        return $this;
//    }

}
