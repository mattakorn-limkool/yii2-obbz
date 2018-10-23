<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\behaviors;
use obbz\yii2\models\Rating;
use obbz\yii2\models\RatingAggregate;
use obbz\yii2\utils\ObbzYii;
use yii\base\Behavior;
use yii\helpers\ArrayHelper;

class RatingBehavior extends Behavior
{
    public $entities = [];

    /**
     * example
     *  [
            ['label'=>'5', 'value'=>5],
            ['label'=>'4', 'value'=>4],
            ['label'=>'3', 'value'=>3],
            ['label'=>'2', 'value'=>2],
            ['label'=>'1', 'value'=>1],
        ]
     *
     * @var array
     */
    public $configResultItems = [];
    /**
     * @var array
     */
    protected $rateAttributes;

    protected $resultItems;

    /**
     * @param \yii\base\Component $owner
     */
    public function attach($owner)
    {
        parent::attach($owner);
    }

    public function getRatingAggregate($name)
    {
        if (in_array($name, $this->entities)) {
            return new RatingAggregate([
                'entity' => $name,
                'target_id' => $this->owner->getPrimaryKey(),
                'version' => ArrayHelper::getValue($this->rateAttributes, ["{$name}Version"]),
                'value' => ArrayHelper::getValue($this->rateAttributes, ["{$name}Value"]),
                'amount' => ArrayHelper::getValue($this->rateAttributes, ["{$name}Amount"]),
                'rating' => ArrayHelper::getValue($this->rateAttributes, ["{$name}Rating"]),
            ]);
        }
        return null;
    }


    /**
     * @param $entity
     * @return null|integer
     * @throws \yii\base\InvalidConfigException
     */
    public function getRatingUserValue($entity)
    {
//        return $this->rateAttributes;
        return ArrayHelper::getValue($this->rateAttributes, "{$entity}UserValue");
//        return null;
    }

    public function getResultItems($entity, $version = 1){
        if(!isset($this->resultItems)){
            $primaryKey = $this->owner->primaryKey()[0];
            $sumData = Rating::find()
                ->select('count(*) as countRecord, value')
                ->where(['entity'=>$entity, 'version'=>1, 'target_id' => $this->owner->$primaryKey])
                ->groupBy(['value'])->all()
            ;
//            ObbzYii::debugModels($sumData);
            $totalAmount = 0;
            foreach($this->configResultItems as $config){
                // make default result

                $this->resultItems[$config['value']] = [
                    'value' => $config['value'],
                    'label' => $config['label'],
                    'progressbarClass' => isset($config['progressbarClass']) ? $config['progressbarClass'] : 'progressbar',
                    'amount' => 0,
                    'percent' => 0,
                ];
                if(is_string($config['value'])){ // support range
                    $range = explode('-',$config['value']);
                    foreach($sumData as $queryRating){
                        if($queryRating->value >= $range[0] && $queryRating->value <= $range[1] ){
                            $this->resultItems[$config['value']]['amount'] += $queryRating->countRecord;
                            $totalAmount +=  $queryRating->countRecord;
                            break;
                        }
                    }
                }else{ // excelty match
                    foreach($sumData as $queryRating){
                        if($config['value'] == $queryRating->value){
                            $this->resultItems[$config['value']]['amount'] = $queryRating->countRecord;
                            $totalAmount +=  $queryRating->countRecord;
                            break;
                        }
                    }
                }


            }
            // calculate percent
            foreach($this->resultItems as $key => $resultItem){
                if($resultItem['amount'] > 0){
                    $this->resultItems[$key]['percent'] = ($resultItem['amount']/$totalAmount) * 100;
                }
            }

        }
        return $this->resultItems;
    }

    protected function checkAttribute($name)
    {

        foreach ($this->entities as $entity) {
            if ($name == "{$entity}Value" || $name == "{$entity}Amount" || $name == "{$entity}Rating" ||
                $name == "{$entity}Version" || $name == "{$entity}UserValue") {
                return true;
            }
        }
        return false;
    }


    /**
     * @param string $name
     * @param mixed $value
     * @throws \yii\base\UnknownPropertyException
     */
    public function __set($name, $value)
    {
        if ($this->checkAttribute($name)) {
            $this->rateAttributes[$name] = !is_null($value) ?  $value : null;
        } else {
            parent::__set($name, $value);
        }
    }

    /**
     * @param string $name
     * @param bool|true $checkVars
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function canGetProperty($name, $checkVars = true)
    {
        if (isset($this->rateAttributes[$name]) || $this->checkAttribute($name)) {
            return true;
        }
        return parent::canGetProperty($name, $checkVars);
    }

    /**
     * @param string $name
     * @param bool|true $checkVars
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function canSetProperty($name, $checkVars = true)
    {
        if ($this->checkAttribute($name)) {
            return true;
        }
        return parent::canSetProperty($name, $checkVars);
    }
}