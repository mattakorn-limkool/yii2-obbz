<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\widgets;


use obbz\yii2\behaviors\RatingBehavior;
use obbz\yii2\models\CoreActiveRecord;
use obbz\yii2\utils\ObbzYii;
use yii\base\Widget;
use yii\helpers\Url;
use yii\web\JsExpression;

class RatingWidget extends Widget
{
    public $entity;
    /**
     * @var CoreActiveRecord
     */
    public $model;
    public $version = 1;
    public $ajaxSubmit = true;
    public $ajaxSubmitUrl = ['site/rating'];
    public $pluginOptions = [];
    public $pluginEvents = [];

    public $viewFile = '@vendor/obbz/yii2/widgets/views/rating';

    public $userValue;

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
            $this->userValue = $this->isBehaviorIncluded() ? $this->model->getRatingUserValue($this->entity) : null;
        }
    }


    public function run()
    {

        return $this->render($this->viewFile, [
            'pluginOptions'=>array_merge($this->getDefaultPluginOptions(), $this->pluginOptions),
            'pluginEvents'=>array_merge($this->getDefaultPluginEvents(), $this->pluginEvents),
            'userValue' =>$this->userValue
        ]);
    }

    public function getDefaultPluginOptions(){
        return [
            'theme' => 'krajee-fa', // 'krajee-svg', 'krajee-uni', 'krajee-fa'
            'size' => 'sm',
            'showCaption'=>false,
            'step' => 1,
            'min' => 0,
            'max' => 5,
            'stars' => 5,
        ];
    }

    public function getDefaultPluginEvents(){
        if($this->ajaxSubmitUrl){
            $modelPk = $this->model->getPrimaryKey();
            $ajaxSubmitUrl = Url::to($this->ajaxSubmitUrl + ['entity'=>$this->entity, 'target_id'=>$modelPk, 'version'=>$this->version]);
            $submitJs = new JsExpression("function(event, starValue) {
                jQuery.ajax({
                    url: '$ajaxSubmitUrl&action=' + event.type + '&value=' + starValue, type: 'POST', dataType: 'json', cache: false,
                    data: {}
                });
            }");

            return $pluginEvents = [
                "rating:change" => $submitJs,
                "rating:clear" => $submitJs,
                "rating:reset" => $submitJs,
            ];
        }else{
            return [];
        }

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