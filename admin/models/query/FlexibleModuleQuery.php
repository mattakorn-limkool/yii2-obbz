<?php

namespace obbz\yii2\admin\models\query;
use \obbz\yii2\admin\models\FlexibleModule;


/**
 * This is the ActiveQuery class for [[\obbz\yii2\admin\models\FlexibleModule]].
 *
 * @see \obbz\yii2\admin\models\FlexibleModule
 */
class FlexibleModuleQuery extends \obbz\yii2\models\CoreActiveQuery
{

    /**
     * @inheritdoc
     * @return \obbz\yii2\admin\models\FlexibleModule[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \obbz\yii2\admin\models\FlexibleModule|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }



//    public function defaultOrder(){
//        $t = FlexibleModule::tableName();
//        $this->orderBy([
//            "{$t}.id"=>SORT_ASC
//        ]);
//        return $this;
//    }


}
