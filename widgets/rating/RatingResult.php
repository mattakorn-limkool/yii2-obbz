<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\widgets\rating;



use obbz\yii2\utils\ObbzYii;

class RatingResult extends Rating
{
    public $viewFile = '@vendor/obbz/yii2/widgets/rating/views/result';

    public $resultItems;

    public function init(){
        parent::init();

        if(!isset($this->resultItems)){
            $this->resultItems = $this->isBehaviorIncluded() ? $this->model->getResultItems($this->entity, $this->version) : [];
        }

        RatingAsset::register($this->getView());
        $this->registerScript();
    }

    public function run()
    {
        return $this->render($this->viewFile, [
            'widgetId' => $this->getId(),
            'userValue' =>$this->userValue,
            'ratingAggregate' =>$this->ratingAggregate,
            'maxValue' =>$this->maxValue,
            'resultItems' =>$this->resultItems,
        ]);
    }

    protected function registerScript()
    {
//        $js = "jQuery('#{$this->getId()} .circlechart').circlechart();";
//        $this->getView()->registerJs($js);
    }
}