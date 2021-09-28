<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\behaviors;

use obbz\yii2\utils\ObbzYii;
use yii\base\Behavior;

class AdsBehavior extends Behavior
{
    public $reachAttribute = 'reach_count';
    public $showAttribute = 'show_count';
    public $touchAttribute = 'touch_count';

    public function init()
    {
        parent::init();

        if ($this->reachAttribute == null or $this->showAttribute == null or $this->touchAttribute == null) {
            throw new InvalidConfigException('The "reachedAttribute", "showAttribute" and "touchAttribute" property must be set.');
        }

    }

    /***
     * @param array|null $ids can set to update all model in same time
     */
    public function addShowCount(array $ids = null){
        $counterArr = [
            $this->showAttribute => 1,
            $this->reachAttribute => 1,
        ];

        if(is_array($ids)){
            $modelClass = $this->owner->className();
            $modelClass::updateAllCounters($counterArr, ['id'=>$ids]);
        }else{
            $this->owner->updateCounters($counterArr);
        }

    }


    /***
     * @param array|null $ids can set to update all model in same time
     */
    public function addTouchCount(array $ids = null){
        $counterArr = [
            $this->touchAttribute => 1,
            $this->reachAttribute => 1,
        ];

        if(is_array($ids)){
            $modelClass = $this->owner->className();
            $modelClass::updateAllCounters($counterArr, ['id'=>$ids]);
        }else{
            $this->owner->updateCounters($counterArr);
        }

    }




}