<?php
namespace obbz\yii2\extensions\fancybox5;
use yii\base\Widget;

/**
 * @author  Mattakorn Limkool <obbz.dev@gmail.com>
 *
 */
class Fancybox extends Widget
{
    public $target = "[data-fancybox]";
    public $clientOptions = [];

    public function run()
    {
        $this->registerPlugin();
    }

    protected function registerPlugin()
    {
        $js = [];
        $view = $this->getView();
        FancyboxAsset::register($view);

        $target = $this->target;

        $options = $this->clientOptions !== false && !empty($this->clientOptions)
            ? Json::encode($this->clientOptions)
            : '{}';


        $js[] = "Fancybox.bind('$target', $options);";
        $view->registerJs(implode("\n", $js));
    }
}