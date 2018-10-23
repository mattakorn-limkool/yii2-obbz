<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\widgets\rating;


use obbz\yii2\behaviors\RatingBehavior;
use obbz\yii2\models\CoreActiveRecord;
use obbz\yii2\utils\ObbzYii;
use yii\base\Widget;
use yii\helpers\Url;
use yii\web\JsExpression;

class Rating extends Widget
{
    public $entity;
    /**
     * @var CoreActiveRecord
     */
    public $model;
    public $version = 1;
    public $pluginOptions = [];
    public $pluginEvents = [];
    public $viewFile = '@vendor/obbz/yii2/widgets/rating/views/rating';
    public $minValue = 0;
    public $maxValue = 5;
    public $stepValue = 1;
    public $stars = 5;


    public $userValue;
    public $ratingAggregate;

    /**
     * @var bool
     */
    protected $_behaviorIncluded;

    /**
     * @inheritdoc
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();

        if (!isset($this->entity) || !isset($this->model)) {
            throw new InvalidParamException(Yii::t('vote', 'Entity and model must be set.'));
        }

        if (!isset($this->userValue)) {
            $this->userValue = $this->isBehaviorIncluded() ? $this->model->getRatingUserValue($this->entity, $this->version) : null;
        }
        if(!isset($this->ratingAggregate)){
            $this->ratingAggregate = $this->isBehaviorIncluded() ? $this->model->getRatingAggregate($this->entity, $this->version) : null;
        }
    }


    public function getDefaultPluginOptions(){
        return [];
    }

    public function getDefaultPluginEvents(){
        return [];
    }


    /**
     * @return bool
     */
    protected function isBehaviorIncluded()
    {
        if (isset($this->_behaviorIncluded)) {
            return $this->_behaviorIncluded;
        }

        if (!isset($this->aggregateModel) || !isset($this->userValue)) {
            foreach ($this->model->getBehaviors() as $behavior) {
                if ($behavior instanceof RatingBehavior) {
                    return $this->_behaviorIncluded = true;
                }
            }
        }

        return $this->_behaviorIncluded = false;
    }
}