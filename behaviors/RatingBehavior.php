<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\behaviors;
use obbz\yii2\models\RatingAggregate;
use yii\base\Behavior;
use yii\helpers\ArrayHelper;

class RatingBehavior extends Behavior
{
    public $entities = [];
    /**
     * @var array
     */
    protected $rateAttributes;

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
            $this->rateAttributes[$name] = !is_null($value) ? (int) $value : null;
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