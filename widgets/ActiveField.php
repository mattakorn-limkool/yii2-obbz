<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace obbz\yii2\widgets;

use kartik\datetime\DateTimePicker;
use kartik\time\TimePicker;
use kartik\widgets\DatePicker;
use kartik\widgets\TouchSpin;
use obbz\yii2\extensions\ckeditor\CoreCKEditor;
use obbz\yii2\i18n\CoreFormatter;
use obbz\yii2\utils\ObbzYii;
use pudinglabs\tagsinput\TagsinputWidget;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\captcha\Captcha;
use yii\helpers\Url;
use yii\web\JsExpression;

class ActiveField extends \yii\widgets\ActiveField
{
    public $options = ['class' => 'form-group'];
    public $template = "{label}\n{input}\n{hint}\n{error}";
    public $labelOptions = [];
    /**
     * @var boolean whether to render [[checkboxList()]] and [[radioList()]] inline.
     */
    public $inline = false;
    /**
     * @var string|null optional template to render the `{input}` placeholder content
     */
    public $inputTemplate;
    /**
     * @var array options for the wrapper tag, used in the `{beginWrapper}` placeholder
     */
    public $wrapperOptions = [];
    /**
     * @var null|array CSS grid classes for horizontal layout. This must be an array with these keys:
     *  - 'offset' the offset grid class to append to the wrapper if no label is rendered
     *  - 'label' the label grid class
     *  - 'wrapper' the wrapper grid class
     *  - 'error' the error grid class
     *  - 'hint' the hint grid class
     */
    public $horizontalCssClasses;
    /**
     * @var string the template for checkboxes in default layout
     */
    public $checkboxTemplate = "<div class=\"checkbox\">\n{beginLabel}\n{input}<i class=\"input-helper\"></i>\n{labelTitle}\n{endLabel}\n{error}\n{hint}\n</div>";
    /**
     * @var string the template for radios in default layout
     */
    public $radioTemplate = "<div class=\"radio\">\n{beginLabel}\n{input}<i class=\"input-helper\"></i>\n{labelTitle}\n{endLabel}\n{error}\n{hint}\n</div>";
    /**
     * @var string the template for checkboxes in horizontal layout
     */
    public $horizontalCheckboxTemplate = "{beginWrapper}\n<div class=\"checkbox\">\n{beginLabel}\n{input}\n{labelTitle}\n{endLabel}\n</div>\n{error}\n{endWrapper}\n{hint}";
    /**
     * @var string the template for radio buttons in horizontal layout
     */
    public $horizontalRadioTemplate = "{beginWrapper}\n<div class=\"radio\">\n{beginLabel}\n{input}\n{labelTitle}\n{endLabel}\n</div>\n{error}\n{endWrapper}\n{hint}";
    /**
     * @var string the template for inline checkboxLists
     */
    public $inlineCheckboxListTemplate = "{label}\n{beginWrapper}\n{input}\n{error}\n{endWrapper}\n{hint}";
    /**
     * @var string the template for inline radioLists
     */
    public $inlineRadioListTemplate = "{label}\n{beginWrapper}\n{input}\n{error}\n{endWrapper}\n{hint}";
    /**
     * @var boolean whether to render the error. Default is `true` except for layout `inline`.
     */
    public $enableError = true;
    /**
     * @var boolean whether to render the label. Default is `true`.
     */
    public $enableLabel = true;

    public $widgetTemplate = "{label}\n{input}\n{hint}\n{error}";
    public $captchaTemplate = "{label}\n<div class=\"row\">{input}</div>\n{hint}\n{error}";



    /**
     * @inheritdoc
     */
    public function __construct($config = [])
    {
        $layoutConfig = $this->createLayoutConfig($config);
        $config = ArrayHelper::merge($layoutConfig, $config);
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function render($content = null)
    {
        if ($content === null) {
            if (!isset($this->parts['{beginWrapper}'])) {
                $options = $this->wrapperOptions;
                $tag = ArrayHelper::remove($options, 'tag', 'div');
                $this->parts['{beginWrapper}'] = Html::beginTag($tag, $options);
                $this->parts['{endWrapper}'] = Html::endTag($tag);
            }
            if ($this->enableLabel === false) {
                $this->parts['{label}'] = '';
                $this->parts['{beginLabel}'] = '';
                $this->parts['{labelTitle}'] = '';
                $this->parts['{endLabel}'] = '';
            } elseif (!isset($this->parts['{beginLabel}'])) {
                $this->renderLabelParts();
            }
            if ($this->enableError === false) {
                $this->parts['{error}'] = '';
            }
            if ($this->inputTemplate) {
                $input = isset($this->parts['{input}']) ?
                    $this->parts['{input}'] : Html::activeTextInput($this->model, $this->attribute, $this->inputOptions);
                $this->parts['{input}'] = strtr($this->inputTemplate, ['{input}' => $input]);
            }
        }
        return parent::render($content);
    }

    /**
     * @inheritdoc
     */
    public function dropDownList($items, $options = [])
    {

//        if($this->form->layout === 'default'){
//            $this->label(true);
//            $this->options = ['class' => 'form-group'];
//        }

        // parent dropDownList

//        $options = array_merge($this->inputOptions, $options);
//        $this->addAriaAttributes($options);
//        $this->adjustLabelFor($options);
//        $this->parts['{input}'] = Html::activeDropDownList($this->model, $this->attribute, $items, $options);
//        $this->parts['{input}'] = '<div class="select">'. . '</div>';

        return parent::dropDownList($items, $options);
    }


    /**
     * @inheritdoc
     */
    public function checkbox($options = [], $enclosedByLabel = true)
    {
        if ($enclosedByLabel) {
            if (!isset($options['template'])) {
                $this->template = $this->form->layout === 'horizontal' ?
                    $this->horizontalCheckboxTemplate : $this->checkboxTemplate;
            } else {
                $this->template = $options['template'];
                unset($options['template']);
            }
            if (isset($options['label'])) {
                $this->parts['{labelTitle}'] = $options['label'];
            }
            if ($this->form->layout === 'horizontal') {
                Html::addCssClass($this->wrapperOptions, $this->horizontalCssClasses['offset']);
            }
            $this->labelOptions['class'] = null;
        }

        return parent::checkbox($options, false);
    }

    /**
     * @inheritdoc
     */
    public function radio($options = [], $enclosedByLabel = true)
    {
        if ($enclosedByLabel) {
            if (!isset($options['template'])) {
                $this->template = $this->form->layout === 'horizontal' ?
                    $this->horizontalRadioTemplate : $this->radioTemplate;
            } else {
                $this->template = $options['template'];
                unset($options['template']);
            }
            if (isset($options['label'])) {
                $this->parts['{labelTitle}'] = $options['label'];
            }
            if ($this->form->layout === 'horizontal') {
                Html::addCssClass($this->wrapperOptions, $this->horizontalCssClasses['offset']);
            }
            $this->labelOptions['class'] = null;
        }

        return parent::radio($options, false);
    }

    /**
     * @inheritdoc
     */
    public function checkboxList($items, $options = [])
    {
        if ($this->inline) {
            if (!isset($options['template'])) {
                $this->template = $this->inlineCheckboxListTemplate;
            } else {
                $this->template = $options['template'];
                unset($options['template']);
            }
            if (!isset($options['itemOptions'])) {
                $options['itemOptions'] = [
                    'labelOptions' => ['class' => 'checkbox-inline'],
                ];
            }
        }  elseif (!isset($options['item'])) {
            $itemOptions = isset($options['itemOptions']) ? $options['itemOptions'] : [];
            $options['item'] = function ($index, $label, $name, $checked, $value) use ($itemOptions) {
                $options = array_merge(['label' => $label, 'value' => $value], $itemOptions);
                return '<div class="checkbox">' . Html::checkbox($name, $checked, $options) . '</div>';
            };
        }
        parent::checkboxList($items, $options);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function radioList($items, $options = [])
    {
        if ($this->inline) {
            if (!isset($options['template'])) {
                $this->template = $this->inlineRadioListTemplate;
            } else {
                $this->template = $options['template'];
                unset($options['template']);
            }
            if (!isset($options['itemOptions'])) {
                $options['itemOptions'] = [
                    'labelOptions' => ['class' => 'radio-inline'],
                ];
            }
        }  elseif (!isset($options['item'])) {
            $itemOptions = isset($options['itemOptions']) ? $options['itemOptions'] : [];
            $options['item'] = function ($index, $label, $name, $checked, $value) use ($itemOptions) {
                $options = array_merge(['label' => $label, 'value' => $value], $itemOptions);
                return '<div class="radio">' . Html::radio($name, $checked, $options) . '</div>';
            };
        }
        parent::radioList($items, $options);
        return $this;
    }

    /**
     * Renders Bootstrap static form control.
     * @param array $options the tag options in terms of name-value pairs. These will be rendered as
     * the attributes of the resulting tag. There are also a special options:
     *
     * - encode: boolean, whether value should be HTML-encoded or not.
     *
     * @return $this the field object itself
     * @since 2.0.5
     * @see http://getbootstrap.com/css/#forms-controls-static
     */
    public function staticControl($options = [])
    {
        $this->adjustLabelFor($options);
        $this->parts['{input}'] = Html::activeStaticControl($this->model, $this->attribute, $options);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function label($label = null, $options = [])
    {
        if (is_bool($label)) {
            $this->enableLabel = $label;
            if ($label === false && $this->form->layout === 'horizontal') {
                Html::addCssClass($this->wrapperOptions, $this->horizontalCssClasses['offset']);
            }
        } else {
            $this->enableLabel = true;
            $this->renderLabelParts($label, $options);
            parent::label($label, $options);
        }
        return $this;
    }

    /**
     * upload input file/img
     * @param array $options
     * @return $this
     */
    public function fileInput($options = [])
    {
        $this->template = "{label}\n{input}\n{hint}\n{error}";
        $labelName = $this->model->getAttributeLabel($this->attribute);
        // https://github.com/yiisoft/yii2/pull/795
        if ($this->inputOptions !== ['class' => 'form-control']) {
            $options = array_merge($this->inputOptions, $options);
        }
        // https://github.com/yiisoft/yii2/issues/8779
        if (!isset($this->form->options['enctype'])) {
            $this->form->options['enctype'] = 'multipart/form-data';
        }
        $this->addAriaAttributes($options);
        $this->adjustLabelFor($options);
        $this->parts['{input}'] = '<div class="fileinput fileinput-new" data-provides="fileinput">
                    <span class="btn btn-primary btn-file m-r-10">
                        <span class="fileinput-new">'. \Yii::t('obbz', 'Select {label}',['label'=>$labelName])  .'</span>
                        <span class="fileinput-exists">'. \Yii::t('obbz', 'Change') .'</span>
                        '. Html::activeFileInput($this->model, $this->attribute, $options) .'
                    </span>
                    <span class="fileinput-filename"></span>
                    <a href="#" class="close fileinput-exists" data-dismiss="fileinput">&times;</a>
                </div>';

        return $this;

    }

    /**
     * upload input img with previewer
     * @param string $thumb
     *          thumb = default thumb name is thumb
     *          null = real upload file
     *          other = custom thumb name
     *
     * @param array $options
     * @return $this
     */
    public function imgInput($thumb = 'thumb', $options = []){
        if(!isset($thumb)){ // == null
            $imgPath = $this->model->getUploadUrl($this->attribute);
        }else{
            $imgPath = $this->model->getThumbUploadUrl($this->attribute, $thumb);
        }

        $this->template = "{label}\n{input}\n{hint}\n{error}";
        $labelName = $this->model->getAttributeLabel($this->attribute);

        // https://github.com/yiisoft/yii2/pull/795
        if ($this->inputOptions !== ['class' => 'form-control']) {
            $options = array_merge($this->inputOptions, $options);
        }
        // https://github.com/yiisoft/yii2/issues/8779
        if (!isset($this->form->options['enctype'])) {
            $this->form->options['enctype'] = 'multipart/form-data';
        }
        $this->addAriaAttributes($options);
        $this->adjustLabelFor($options);
        $this->parts['{input}'] = '<div class="fileinput fileinput-new" data-provides="fileinput">
                                 <div class="fileinput-new thumbnail no-border no-padding" data-trigger="fileinput">
									'. Html::img($imgPath) .'
								</div>

                                <div class="fileinput-preview fileinput-exists thumbnail" data-trigger="fileinput"></div>
                                <div>
                                    <span class="btn btn-primary btn-file">
                                       <span class="fileinput-new">'. \Yii::t('obbz', 'Select {label}',['label'=>$labelName])  .'</span>
                                        <span class="fileinput-exists">' . \Yii::t('obbz', 'Change') . '</span>
                                        '. Html::activeFileInput($this->model, $this->attribute, $options) .'
                                    </span>
                                    <a href="#" class="btn btn-danger fileinput-exists" data-dismiss="fileinput">Remove</a>
                                </div>
                            </div>';

        return $this;
    }


    /**
     * @param boolean $value whether to render a inline list
     * @return $this the field object itself
     * Make sure you call this method before [[checkboxList()]] or [[radioList()]] to have any effect.
     */
    public function inline($value = true)
    {
        $this->inline = (bool) $value;
        return $this;
    }

    public function widget($class, $config = []){
//        $this->label(true);
//        $this->options = ['class' => 'form-group fg-padding'];

        $this->template = $this->widgetTemplate;
        return parent::widget($class, $config);
    }

    public function captcha($config = []){
        $this->template = $this->captchaTemplate;
//        $this->enableLabel = false;
        $config['template'] = isset($config['template']) ?
            $config['template'] :
            '<div class="col-sm-3">{image}</div><div class="col-sm-9"><div class="fg-line">{input}</div></div>';

        return parent::widget(Captcha::className(), $config);
    }

    public function rte($config = []){

        return $this->widget(CoreCKEditor::className(), array_merge([
            'options' => ['rows' => 6],
            'preset' => ObbzYii::user()->can(\backend\components\Roles::THE_CREATOR) ? 'full' : 'basic',
            'clientOptions' => [
                'filebrowserUploadUrl' => Url::to(['/site/ckeditor-upload-img'])
            ]
        ], $config));
    }

    public function datePicker($config=[]){
        $dateFormat = ObbzYii::formatter()->dateFormat;
        return $this->widget(DatePicker::className(), array_merge([
            'type' => DatePicker::TYPE_COMPONENT_APPEND,
            'pluginOptions' => [
                'autoclose' => true,
                'format' => ObbzYii::formatter()->convertDateYiiToBsDatepicker($dateFormat),
            ]
        ], $config));
    }

    public function timePicker($config=[]){
        $dateFormat = ObbzYii::formatter()->timeFormat;
        return parent::widget(TimePicker::className(), array_merge([
            'name' => $this->attribute,
            'pluginOptions' => [
                'showMeridian' => false,
//                'format' => ObbzYii::formatter()->convertDateYiiToBsDatepicker($dateFormat),
            ]

        ], $config));
    }

    public function dateTimePicker($config=[]){
        $datetimeFormat = ObbzYii::formatter()->datetimeFormat;
        return $this->widget(DateTimePicker::className(), array_merge([
            'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
            'pluginOptions' => [
                'autoclose' => true,
                'format' => ObbzYii::formatter()->convertDateYiiToBsDatepicker($datetimeFormat),
            ]
        ], $config));
    }

    public function dateRangePicker(){

    }

    /**
     * @doc https://github.com/pudinglabs/yii2-bootstrap-tags-input
     * @param array $config  -  'options' => [],
                                'clientOptions' => [],
                                'clientEvents' => []
     *
     * @return $this
     */
    public function tagsInput($config=[]){

        return $this->widget(TagsinputWidget::className(), array_merge([

        ], $config));
    }

    public function autoCompleteWithId($idAttribute, $data, $config = []){
        return $this->widget(
            AutoCompleteWithId::className(),
            array_merge([
                'model'=> $this->model,
                'name' => $this->attribute,
                'idAttribute' => $idAttribute,
                'options' => $this->inputOptions,
                'clientOptions' => [
                    'source' => $data,
                    'autoFill'=>true,
                    'appendTo'=>'#'.$this->form->id
                    ],
            ], $config)
        );
    }

    /**
     * @param $idAttribute
     * @param array $url
     * @param array $config
     * @return $this
     */
    public function ajaxAutoComplete($idAttribute, $url, $config=[]){

        return $this->widget(
            AutoCompleteAjax::className(),
            array_merge([
                'idAttribute' => $idAttribute,
                'url' => $url,
                'multiple' => false,
            ], $config)
        );
    }


    public function touchSpin($config=[]){

        return $this->widget(TouchSpin::className(), array_merge([

        ], $config));
    }

    /**
     * @param array $instanceConfig the configuration passed to this instance's constructor
     * @return array the layout specific default configuration for this instance
     */
    protected function createLayoutConfig($instanceConfig)
    {
        $config = [
            'hintOptions' => [
                'tag' => 'p',
                'class' => 'help-block hint-block',
            ],
            'errorOptions' => [
                'tag' => 'p',
                'class' => 'help-block help-block-error',
            ],
            'inputOptions' => [
                'class' => 'form-control fg-input',
            ],
        ];

        $layout = $instanceConfig['form']->layout;

        if ($layout === 'horizontal') {
            $config['template'] = "{label}\n{beginWrapper}\n{input}\n{error}\n{endWrapper}\n{hint}";
            $cssClasses = [
                'offset' => 'col-sm-offset-3',
                'label' => 'col-sm-3',
                'wrapper' => 'col-sm-6',
                'error' => '',
                'hint' => 'col-sm-3',
            ];
            if (isset($instanceConfig['horizontalCssClasses'])) {
                $cssClasses = ArrayHelper::merge($cssClasses, $instanceConfig['horizontalCssClasses']);
            }
            $config['horizontalCssClasses'] = $cssClasses;
            $config['wrapperOptions'] = ['class' => $cssClasses['wrapper']];
            $config['labelOptions'] = ['class' => 'control-label ' . $cssClasses['label']];
            $config['errorOptions'] = ['class' => 'help-block help-block-error ' . $cssClasses['error']];
            $config['hintOptions'] = ['class' => 'help-block hint-block ' . $cssClasses['hint']];
        } elseif ($layout === 'inline') {
//            $config['labelOptions'] = ['class' => 'sr-only'];
//            $config['enableError'] = false;
        }

        return $config;
    }

    /**
     * @param string|null $label the label or null to use model label
     * @param array $options the tag options
     */
    protected function renderLabelParts($label = null, $options = [])
    {
        $options = array_merge($this->labelOptions, $options);
        if ($label === null) {
            if (isset($options['label'])) {
                $label = $options['label'];
                unset($options['label']);
            } else {
                $attribute = Html::getAttributeName($this->attribute);
                $label = Html::encode($this->model->getAttributeLabel($attribute));
            }
        }
        if (!isset($options['for'])) {
            $options['for'] = Html::getInputId($this->model, $this->attribute);
        }
        $this->parts['{beginLabel}'] = Html::beginTag('label', $options);
        $this->parts['{endLabel}'] = Html::endTag('label');
        if (!isset($this->parts['{labelTitle}'])) {
            $this->parts['{labelTitle}'] = $label;
        }
    }
}
