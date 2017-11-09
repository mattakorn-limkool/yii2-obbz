<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\widgets\tagsinput;


use pudinglabs\tagsinput\TagsinputWidget;
use yii\helpers\Html;

class TagsInputWidgets  extends TagsinputWidget
{
    public function init()
    {
        if (!isset($this->options['id'])) {
            if ($this->hasModel()) {
                $this->options['id'] = Html::getInputId($this->model, $this->attribute);
            } else {
                $this->options['id'] = $this->getId();
            }
        }
        TagsInputAsset::register($this->getView());
        $this->registerScript();
        $this->registerEvent();
    }
}