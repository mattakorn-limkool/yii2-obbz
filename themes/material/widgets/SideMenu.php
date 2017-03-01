<?php
/**
 * Created by PhpStorm.
 * User: mattakorn
 * Date: 17/2/2560
 * Time: 17:05
 */

namespace obbz\yii2\themes\material\widgets;


use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Menu;

class SideMenu extends Menu
{
    public $options = array(
        'class'=>'main-menu'
    );


    protected function renderItem($item)
    {

        $iconLabel = "";
        if(isset($item['icon']))
            $iconLabel = '<i class="zmdi zmdi-'. $item['icon'] .'"></i> ';

        if (isset($item['url'])) {
            $template = ArrayHelper::getValue($item, 'template', $this->linkTemplate);

            return strtr($template, [
                '{url}' => Html::encode(Url::to($item['url'])),
                '{label}' => $iconLabel . $item['label'],
            ]);
        } else {
            $template = ArrayHelper::getValue($item, 'template', $this->labelTemplate);

            return strtr($template, [
                '{label}' => $iconLabel . $item['label'],
            ]);
        }
    }
}