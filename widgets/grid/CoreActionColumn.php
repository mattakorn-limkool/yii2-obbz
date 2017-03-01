<?php
/**
 * for support front awesome
 */

namespace obbz\yii2\widgets\grid;


use obbz\yii2\models\CoreActiveRecord;
use yii\base\ErrorException;
use yii\grid\ActionColumn;
use yii\helpers\Html;
use Yii;
use yii\helpers\Url;

class CoreActionColumn extends ActionColumn
{
    public $template = '{publish}{unpublish} {update} {delete}';
    public $contentOptions = ['class'=>'action-column-row'];

    // header config
    public $headerOptions = ['class'=>'text-right'];
    public $enableHeaderAction = true;
    public $headerActionButtons = [];
    public $headerActionTemplate = '<div class="core-action-selected">{publish-selected} {unpublish-selected} {delete-selected}</div>';
    public $headerButtonOptions = [];


    protected function initDefaultButtons()
    {

        $this->initDefaultButton('view', 'search');
        $this->initDefaultButton('publish', 'check');
        $this->initDefaultButton('unpublish', 'close');
        $this->initDefaultButton('update', 'pencil');
        $this->initDefaultButton('delete', 'trash', [
            'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
            'data-method' => 'post',
        ]);

        if($this->enableHeaderAction){
            /** @var CoreGridView $grid */
            $grid = $this->grid;
            if($grid->enableSelectedAction){
                $this->initDefaultHeaderButton('publish-selected', 'check');
                $this->initDefaultHeaderButton('unpublish-selected', 'close');
                $this->initDefaultHeaderButton('delete-selected', 'trash', [
                    'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                    'data-method' => 'post',
                ]);
            }else{
                throw new ErrorException("ActionColume 'enableSelectedAction' depend on grid, Please set enableSelectedAction=true  on CoreGridView before");
            }
        }
    }

    protected function initDefaultButton($name, $iconName, $additionalOptions = [])
    {

        if(!isset($this->visibleButtons['publish'])){
            $this->visibleButtons['publish'] = function($model, $key, $index){
                /** @var $model CoreActiveRecord */
                return $model->hasUnpublished();
            };
        }
        if(!isset($this->visibleButtons['unpublish'])){
            $this->visibleButtons['unpublish'] = function($model, $key, $index){
                /** @var $model CoreActiveRecord */
                return $model->hasPublished();
            };
        }


        if (!isset($this->buttons[$name]) && strpos($this->template, '{' . $name . '}') !== false) {

            $condArray = [];

            $this->buttons[$name] = function ($url, $model, $key) use ($name, $iconName, $additionalOptions, $condArray) {
                $title = $this->name2Title($name);
                $options = array_merge([
                    'title' => $title,
                    'data-toggle' => 'tooltip',
                    'aria-label' => $title,
                    'data-pjax' => '0',
                    'class'=>'btn btn-icon btn-icon-small btn-action-' . $name,
                    'visible'=>'false',
                ], $additionalOptions, $this->buttonOptions);
                $icon = Html::tag('span', '', ['class' => "fa fa-$iconName"]);
                return Html::a($icon, $url, $options);
            };
        }
    }

    protected function initDefaultHeaderButton($name, $iconName, $additionalOptions = []){
        if (!isset($this->headerActionButtons[$name]) && strpos($this->headerActionTemplate, '{' . $name . '}') !== false) {

            $condArray = [];

            $this->headerActionButtons[$name] = function ($url) use ($name, $iconName, $additionalOptions, $condArray) {
                $title = $this->name2Title($name);
                $options = array_merge([
                    'title' => $title,
                    'data-toggle' => 'tooltip',
                    'aria-label' => $title,
                    'data-pjax' => '0',
                    'class'=>'btn btn-action-' . $name,
                    'visible'=>'false',
                    'onclick'=>'this.form.action="' . $url .'";',
                ], $additionalOptions, $this->headerButtonOptions);
                $icon = Html::tag('span', '', ['class' => "fa fa-$iconName"]);
//                return Html::a($icon, $url, $options);
                return Html::submitButton($icon, $options);
            };
        }
    }

    protected function renderHeaderCellContent()
    {
        if($this->enableHeaderAction){
            return preg_replace_callback('/\\{([\w\-\/]+)\\}/', function ($matches) {
                $name = $matches[1];

                if (isset($this->headerActionButtons[$name])) {
                    $params[0] = $this->controller ? $this->controller . '/' . $name : $name;
                    $url = Url::toRoute($params);
                    return call_user_func($this->headerActionButtons[$name], $url);
                } else {
                    return '';
                }
            }, $this->headerActionTemplate);
        }else{
            return parent::renderHeaderCellContent();
        }
    }

    public function name2Title($name){
        $name = str_replace('-', ' ', $name);
        return \Yii::t('yii', ucfirst($name));
    }
}