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

class HeaderAction extends Menu
{
    /**
     * @var array
     *  string input reder by directry
     *              [
     *                  '<a href=""><i class="zmdi zmdi-trending-up"></i></a>'
     *              ]
     *  array input render by dropdown can be set iconClass and set items for child as string input
     *              [
     *                  [
     *                      'iconClass' => 'zmdi zmdi-more-vert',
     *                      'items' => [
     *                          '<a href="">Item 1</a>',
     *                          '<a href="">Item 2</a>',
     *                      ]
     *                  ]
     *              ]
     */
    public $items = [];
    public $defaultIconClass = 'zmdi zmdi-more-vert';

    public function run()
    {
        $result = '';
        if(!empty($this->items)){
            $result .= '<ul class="actions">';
            foreach($this->items as $action){
                if(is_array($action)){
                    $iconClass = isset($action['iconClass']) ? $action['iconClass'] : $this->defaultIconClass;
                    $childItems = isset($action['items']) ? $action['items'] : [];
                    $result .= '<li class="dropdown">
                                    <a href="" data-toggle="dropdown" aria-expanded="false">
                                        <i class="'. $iconClass .'"></i>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                    ';
                    foreach($childItems as $childItem){
                        $result .= '<li>'. $childItem .'</li>';
                    }
                    $result .= '</ul>';
                }else{ // string
                    $result .= '<li>'. $action .'</li>';
                }
            }
            $result .= '</ul>';
        }
        return $result;
    }
}