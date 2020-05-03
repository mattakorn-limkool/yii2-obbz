<?php

namespace obbz\yii2\widgets\grid;

use obbz\yii2\themes\material\widgets\ActiveForm;
use obbz\yii2\utils\ObbzYii;
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

    /**
     * @var array|string configuration of additional header table rows that will be rendered before the default grid
     * header row. If set as a _string_, it will be displayed as is, without any HTML encoding. If set as an _array_,
     * each row in this array corresponds to a HTML table row, where you can configure the columns with these properties:
     * - `columns`: _array_, the header row columns configuration where you can set the following properties:
     *    - `content`: _string_, the grid cell content for the column
     *    - `tag`: _string_, the tag for rendering the grid cell. If not set, defaults to `th`.
     *    - `options`: _array_, the HTML attributes for the grid cell
     * - `options`: _array_, the HTML attributes for the table row
     */
    public $beforeHeader = [];

    /**
     * @var array|string configuration of additional header table rows that will be rendered after default grid header
     * row. If set as a _string_, it will be displayed as is, without any HTML encoding. If set as an _array_, each
     * row in this array corresponds to a HTML table row, where you can configure the columns with these properties:
     * - `columns`: _array_, the header row columns configuration where you can set the following properties:
     *    - `content`: _string_, the grid cell content for the column
     *    - `tag`: _string_, the tag for rendering the grid cell. If not set, defaults to `th`.
     *    - `options`: _array_, the HTML attributes for the grid cell
     * - `options`: _array_, the HTML attributes for the table row
     */
    public $afterHeader = [];


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



            if($this->sortableFirstColumn){
                array_unshift($this->columns, ['class' => \kotchuprik\sortable\grid\Column::class]);
            }


        }else{
            if(!($this->rowOptions instanceof \Closure)){
                $this->rowOptions = function ($model, $key, $index, $grid) {
                    $rowOptionsArray = $this->rowOptionsInit($model, $key, $index, $grid);
                    return $rowOptionsArray;
                };
            }
        }

        if($this->enableSelectedAction){
            array_unshift($this->columns, [
                'class' => \obbz\yii2\widgets\grid\CoreCheckboxColumn::class,
                'options'=>['style'=>'width: 50px'],
            ]);
        }
        parent::init();
    }

    public function rowOptionsInit($model, $key, $index, $grid){
        // todo - check has declare by widget before
        $rowOptionsArray = [];
        if(isset($model->disabled) && $model->disabled === true){
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

    /**
     * @inheritdoc
     */
    public function renderTableHeader()
    {
        $cells = [];
        foreach ($this->columns as $column) {
            /* @var $column Column */
            $cells[] = $column->renderHeaderCell();
        }
        $content = Html::tag('tr', implode('', $cells), $this->headerRowOptions);
        if ($this->filterPosition === self::FILTER_POS_HEADER) {
            $content = $this->renderFilters() . $content;
        } elseif ($this->filterPosition === self::FILTER_POS_BODY) {
            $content .= $this->renderFilters();
        }

        return "<thead>\n" .
        $this->generateRows($this->beforeHeader) . "\n" .
        $content . "\n" .
        $this->generateRows($this->afterHeader) . "\n" .
        '</thead>';
    }

    /**
     * Generate HTML markup for additional table rows for header and/or footer.
     *
     * @param array|string $data the table rows configuration
     *
     * @return string
     */
    protected function generateRows($data)
    {
        if (empty($data)) {
            return '';
        }
        if (is_string($data)) {
            return $data;
        }
        $rows = '';
        if (is_array($data)) {
            foreach ($data as $row) {
                if (empty($row['columns'])) {
                    continue;
                }
                $rowOptions = ArrayHelper::getValue($row, 'options', []);
                $rows .= Html::beginTag('tr', $rowOptions);
                foreach ($row['columns'] as $col) {
                    $colOptions = ArrayHelper::getValue($col, 'options', []);
                    $colContent = ArrayHelper::getValue($col, 'content', '');
                    $tag = ArrayHelper::getValue($col, 'tag', 'th');
                    $rows .= "\t" . Html::tag($tag, $colContent, $colOptions) . "\n";
                }
                $rows .= Html::endTag('tr') . "\n";
            }
        }
        return $rows;
    }
}