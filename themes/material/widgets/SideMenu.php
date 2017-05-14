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
    public $options = ['class'=>'main-menu'];
    public $activeCssClass = 'active toggled';
    public $activateParents = true;

    protected function renderItems($items)
    {
        $n = count($items);
        $lines = [];
        foreach ($items as $i => $item) {
            if(isset($item['items'])){
                $item['options']['class'] = 'sub-menu';
            }

            $options = array_merge($this->itemOptions, ArrayHelper::getValue($item, 'options', []));
            $tag = ArrayHelper::remove($options, 'tag', 'li');
            $class = [];
            if ($item['active']) {
                $class[] = $this->activeCssClass;
            }
            if ($i === 0 && $this->firstItemCssClass !== null) {
                $class[] = $this->firstItemCssClass;
            }
            if ($i === $n - 1 && $this->lastItemCssClass !== null) {
                $class[] = $this->lastItemCssClass;
            }
            if (!empty($class)) {
                if (empty($options['class'])) {
                    $options['class'] = implode(' ', $class);
                } else {
                    $options['class'] .= ' ' . implode(' ', $class);
                }
            }

            $menu = $this->renderItem($item);
            if (!empty($item['items'])) {
                $submenuTemplate = ArrayHelper::getValue($item, 'submenuTemplate', $this->submenuTemplate);
                $menu .= strtr($submenuTemplate, [
                    '{items}' => $this->renderItems($item['items']),
                ]);
            }
            $lines[] = Html::tag($tag, $menu, $options);
        }

        return implode("\n", $lines);
    }

    protected function renderItem($item)
    {

        $iconLabel = "";
        if(isset($item['icon'])){
            $iconClass = 'zmdi zmdi-';
            $itemIcon = "";
            if(is_array($item['icon'])){
                if($item['icon'][0] == 'fa'){
                    $iconClass = 'fa fa-';
                    $itemIcon = $item['icon'][1];
                }else{
                    $iconClass = $item['icon'][0];
                    $itemIcon = $item['icon'][1];
                }
            }else{
                $itemIcon = $item['icon'];
            }
            $iconLabel = '<i class="'. $iconClass . $itemIcon .'"></i> ';

        }


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