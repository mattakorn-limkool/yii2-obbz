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
    const ACTION_SHOW = 'show';
    const ACTION_TOUCH = 'touch';

    public $reachAttribute = 'reach_count';
    public $showAttribute = 'show_count';
    public $touchAttribute = 'touch_count';

    public $useArea = false;
    public $areaClass = null;
    public $areaRefIdAttribute = 'ads_id';
    public $areaKeyAttribute = 'key_name';
    public $areaReachAttribute = 'reach_count';
    public $areaShowAttribute = 'show_count';
    public $areaTouchAttribute = 'touch_count';


    public function init()
    {
        parent::init();

        if ($this->reachAttribute == null or $this->showAttribute == null or $this->touchAttribute == null) {
            throw new InvalidConfigException('The "reachedAttribute", "showAttribute" and "touchAttribute" property must be set.');
        }

    }

    /***
     * @param array|null $ids can set to update all model in same time
     * @param string|null $areaKey
     */
    public function addShowCount(array $ids = null, $areaKey = null){
        $this->addCount(self::ACTION_SHOW, $ids, $areaKey);
    }


    /***
     * @param array|null $ids can set to update all model in same time
     * @param string|null $areaKey
     */
    public function addTouchCount(array $ids = null, $areaKey = null){
        $this->addCount(self::ACTION_TOUCH, $ids, $areaKey);
    }

    /**
     * @param $action
     * @param array|null $ids
     * @param string|null $areaKey
     * @throws \HttpInvalidParamException
     */
    public function addCount($action, array $ids = null, $areaKey = null){
        if($action == self::ACTION_SHOW){
            $counterArr = [
                $this->showAttribute => 1,
            ];
        }else  if($action == self::ACTION_TOUCH){
            $counterArr = [
                $this->touchAttribute => 1,
            ];
        }else{
            throw new \HttpInvalidParamException('Invalid action');
        }
        $counterArr[$this->reachAttribute] = 1;

        if(is_array($ids)){
            $modelClass = $this->owner->className();
            $modelClass::updateAllCounters($counterArr, ['id'=>$ids]);
            $this->doUseArea($action, $ids, $areaKey);
        }else{
            $this->owner->updateCounters($counterArr);
            $this->doUseArea($action, [$this->owner->id], $areaKey);
        }
    }

    private function doUseArea($action, $ids, $areaKey){
        if($this->useArea) {
            if ($this->areaClass == null) {
                throw new InvalidConfigException('The "areaClass" property must be set.');
            }else if ($this->areaRefIdAttribute == null) {
                throw new InvalidConfigException('The "areaRefIdAttribute" property must be set.');
            }else if ($this->areaKeyAttribute == null) {
                throw new InvalidConfigException('The "areaKeyAttribute" property must be set.');
            } else if ($this->areaReachAttribute == null) {
                throw new InvalidConfigException('The "areaReachAttribute" property must be set.');
            } else if ($this->areaShowAttribute == null) {
                throw new InvalidConfigException('The "areaShowAttribute" property must be set.');
            } else if ($this->areaTouchAttribute == null) {
                throw new InvalidConfigException('The "areaTouchAttribute" property must be set.');
            }

            if($action == self::ACTION_SHOW){
                $counterArr = [
                    $this->areaShowAttribute => 1,
                ];
            }else  if($action == self::ACTION_TOUCH){
                $counterArr = [
                    $this->areaTouchAttribute => 1,
                ];
            }else{
                throw new \HttpInvalidParamException('Invalid action');
            }
            $counterArr[$this->areaReachAttribute] = 1;

            $createIds = [];
            $modelClass = $this->areaClass;
            foreach($ids as $id){

                $areaModel = $modelClass::find()->where([
                    $this->areaRefIdAttribute => $id,
                    $this->areaKeyAttribute => $areaKey,
                ])->one();
                if(empty($areaModel)){
                    $areaModel = new $modelClass;
                    $areaModel->attributes = [
                        $this->areaRefIdAttribute => $id,
                        $this->areaKeyAttribute => $areaKey,
                    ];
                    $areaModel->save(false);
                }
                $areaModel->updateCounters($counterArr);
            }
            $counterArr = [];
//            $modelClass::updateAllCounters($counterArr, [$this->areaRefIdAttribute =>$ids,$this->areaKeyAttribute=> $areaKey]);
        }
    }




}