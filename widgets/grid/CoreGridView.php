<?php

namespace obbz\yii2\widgets\grid;

use obbz\yii2\themes\material\widgets\ActiveForm;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class CoreGridView extends GridView
{
//    public $layout = "{items}\n{pager}\n{summary}";
//    public $layout = "{items}\n{pager}";
    public $sortableEnable = true;
    public $sortableFirstColumn = true;
    /** @var bool - support action column for select checkbox */
    public $enableSelectedAction = true;
    public $formAction = ''; // current submit page
    public $formOptions = [];
    public $additionalUrlParams = [];
    public $tableOptions = ['class' => 'core-grid table table-hover'];


    public function init()
    {

        if($this->sortableEnable){
            if(!($this->rowOptions instanceof \Closure)){
                $this->rowOptions = function ($model, $key, $index, $grid) {
                    $rowOptionsArray = $this->rowOptionsInit($model, $key, $index, $grid);
                    return array_merge($rowOptionsArray, ['data-sortable-id' => $model->id]);
                };
            }

            $this->options =  [
                'data' => [
                    'sortable-widget' => 1,
                    'sortable-url' => \yii\helpers\Url::toRoute(['sorting']),
                ]
            ];
            $pagination = $this->dataProvider->pagination = false;


            if($this->enableSelectedAction){
                array_unshift($this->columns, [
                    'class' => \obbz\yii2\widgets\grid\CoreCheckboxColumn::className(),
                    'options'=>['style'=>'width: 50px'],
                ]);
            }
            if($this->sortableFirstColumn){
                array_unshift($this->columns, ['class' => \kotchuprik\sortable\grid\Column::className()]);
            }


        }else{
            if(!($this->rowOptions instanceof \Closure)){
                $this->rowOptions = function ($model, $key, $index, $grid) {
                    $rowOptionsArray = $this->rowOptionsInit($model, $key, $index, $grid);
                    return $rowOptionsArray;
                };
            }
        }
        parent::init();
    }

    public function rowOptionsInit($model, $key, $index, $grid){
        // todo - check has declare by widget before
        $rowOptionsArray = [];
        if(isset($model->disabled) && $model->disabled){
            $rowOptionsArray['class'] = 'danger';
        }
        return $rowOptionsArray;
    }

    public function renderItems()
    {
        $table = parent::renderItems();
        if($this->enableSelectedAction){
            // will be change by js on submit
            $result = Html::beginForm($this->formAction, 'post', $this->formOptions);
            $result .=  $table;
            $result .= Html::endForm();
        }else{
            $result = $table;
        }

        return $result;
    }
}