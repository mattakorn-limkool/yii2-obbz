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

class TopMainMenu extends Menu
{
    public $options = ['class'=>''];
    public $activeCssClass = 'active';
    public $activateParents = true;
    public $submenuTemplate = "\n<ul class=\"dropdown-menu\" >\n{items}\n</ul>\n";

    public function run()
    {
        if ($this->route === null && \Yii::$app->controller !== null) {
            $this->route = \Yii::$app->controller->getRoute();
        }
        if ($this->params === null) {
            $this->params = \Yii::$app->request->getQueryParams();
        }
        $items = $this->normalizeItems($this->items, $hasActiveChild);
        if (!empty($items)) {
            $options = $this->options;
            $tag = ArrayHelper::remove($options, 'tag', 'ul');
            echo Html::beginTag('nav', ['class'=>'ha-menu']);
            echo Html::tag($tag, $this->renderItems($items), $options);
            echo Html::endTag('nav');
        }
    }

    protected function renderItems($items, $isChild = false)
    {
        $n = count($items);
        $lines = [];
        foreach ($items as $i => $item) {
            $class = ['waves-effect'];
            $isParent = false;
            if(isset($item['items'])){
                $item['options']['class'] = 'dropdown';
                $class = ArrayHelper::remove($class, 'waves-effect');
                $isParent = true;
            }

            if($isChild){
                $class = ArrayHelper::remove($class, 'waves-effect');
            }

            $options = array_merge($this->itemOptions, ArrayHelper::getValue($item, 'options', []));
            $tag = ArrayHelper::remove($options, 'tag', 'li');

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

            $menu = $this->renderItem($item, $isParent);
            if (!empty($item['items'])) {
                $submenuTemplate = ArrayHelper::getValue($item, 'submenuTemplate', $this->submenuTemplate);
                $menu .= strtr($submenuTemplate, [
                    '{items}' => $this->renderItems($item['items'], true),
                ]);
            }
            $lines[] = Html::tag($tag, $menu, $options);
        }

        return implode("\n", $lines);
    }

    protected function renderItem($item, $isParent = false)
    {
        if ($isParent) {
            $template = '<div class="waves-effect" data-toggle="dropdown">{label}</div>';

            return strtr($template, [
                '{label}' => $item['label'],
            ]);
        }
        else if (isset($item['url'])) {
            $template = ArrayHelper::getValue($item, 'template', $this->linkTemplate);

            return strtr($template, [
                '{url}' => Html::encode(Url::to($item['url'])),
                '{label}' => $item['label'],
            ]);
        }
        else {
            $template = ArrayHelper::getValue($item, 'template', $this->labelTemplate);

            return strtr($template, [
                '{label}' => $item['label'],
            ]);
        }
    }

//    protected function renderItem($item)
//    {
//
//        $iconLabel = "";
//        if(isset($item['icon'])){
//            $iconClass = 'zmdi zmdi-';
//            $itemIcon = "";
//            if(is_array($item['icon'])){
//                if($item['icon'][0] == 'fa'){
//                    $iconClass = 'fa fa-';
//                    $itemIcon = $item['icon'][1];
//                }else{
//                    $iconClass = $item['icon'][0];
//                    $itemIcon = $item['icon'][1];
//                }
//            }else{
//                $itemIcon = $item['icon'];
//            }
//            $iconLabel = '<i class="'. $iconClass . $itemIcon .'"></i> ';
//
//        }
//
//
//        if (isset($item['url'])) {
//            $template = ArrayHelper::getValue($item, 'template', $this->linkTemplate);
//
//            return strtr($template, [
//                '{url}' => Html::encode(Url::to($item['url'])),
//                '{label}' => $iconLabel . $item['label'],
//            ]);
//        } else {
//            $template = ArrayHelper::getValue($item, 'template', $this->labelTemplate);
//
//            return strtr($template, [
//                '{label}' => $iconLabel . $item['label'],
//            ]);
//        }
//    }
}