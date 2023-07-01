<?php
/**
 * @author  Mattakorn Limkool <obbz.dev@gmail.com>
 *
 */

namespace obbz\yii2\widgets\flexiblemodule;

use obbz\yii2\admin\models\FlexibleModule;
use obbz\yii2\utils\ObbzYii;
use yii\base\Widget;

class FlexibleModuleRenderer extends Widget
{
    /**
     * raw html will be replace
     * @var
     */
    public $html = '';
    public $modelClass = FlexibleModule::class;
    public $defaultCssClass = 'obbz-flexible-module-render';

    public function init()
    {
        parent::init();
    }

    public function run(){
        /** @var FlexibleModule $modelClass */
        $modelClass = $this->modelClass;
        /** @var FlexibleModule[] $models */
        $models = $modelClass::getSubtituteModels($this->html);

        $replaces = [];
        foreach($models as $model){
            $viewFile = $model->key_name;
            $replaces[] = $this->render($viewFile, compact('model'));
        }

        return $modelClass::replaceSubtituteMarkers($this->html, $replaces);
    }
}