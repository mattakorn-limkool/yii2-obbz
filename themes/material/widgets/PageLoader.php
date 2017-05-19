<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\themes\material\widgets;

use obbz\yii2\utils\ObbzYii;
use yii\base\Widget;

class PageLoader extends Widget
{
    public $skin = "blue";
    public function run()
    {
        return $this->render('page-loader', [
            'skin'=>$this->skin
        ]);

    }
}