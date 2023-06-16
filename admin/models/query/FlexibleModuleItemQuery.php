<?php

namespace obbz\yii2\admin\models\query;
use \obbz\yii2\admin\models\FlexibleModuleItem;


/**
 * This is the ActiveQuery class for [[\obbz\yii2\admin\models\FlexibleModuleItem]].
 *
 * @see \obbz\yii2\admin\models\FlexibleModuleItem
 */
class FlexibleModuleItemQuery extends \obbz\yii2\models\CoreActiveQuery
{

    /**
     * @inheritdoc
     * @return \obbz\yii2\admin\models\FlexibleModuleItem[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \obbz\yii2\admin\models\FlexibleModuleItem|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }



//    public function defaultOrder(){
//        $t = FlexibleModuleItem::tableName();
//        $this->orderBy([
//            "{$t}.id"=>SORT_ASC
//        ]);
//        return $this;
//    }


}
