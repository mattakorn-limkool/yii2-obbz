<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\themes\material\widgets;

use obbz\yii2\utils\ObbzYii;
use yii\base\Widget;

class OutdatedBrowser extends Widget
{

    public function run()
    {
        return $this->render('outdated-browser', [

        ]);

    }
}