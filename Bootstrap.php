<?php


namespace obbz\yii2;

use yii\base\Application;
use yii\base\BootstrapInterface;


/**
 * Class Bootstrap
 */
class Bootstrap implements BootstrapInterface
{

    /**
     * Bootstrap method to be called during application bootstrap stage.
     *
     * @param Application $app the application currently running
     */
    public function bootstrap($app)
    {
        if ($app->hasModule('gii')) {
            if (!isset($app->getModule('gii')->generators['obbzModel'])) {
                $app->getModule('gii')->generators['obbzModel'] = 'obbz\yii2\gii\model\Generator';
                $app->getModule('gii')->generators['obbzCrud'] = 'obbz\yii2\gii\crud\Generator';
                $app->getModule('gii')->generators['obbzNormalModel'] = 'obbz\yii2\gii\normalmodel\Generator';
            }
        }
    }
}