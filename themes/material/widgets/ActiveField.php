<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace obbz\yii2\themes\material\widgets;

use kartik\date\DatePicker;
use kartik\datetime\DateTimePicker;
use kartik\widgets\TimePicker;
use obbz\yii2\extensions\ckeditor\CoreCKEditor;
use obbz\yii2\utils\ObbzYii;
use obbz\yii2\widgets\AutoCompleteAjax;
use obbz\yii2\widgets\AutoCompleteWithId;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\captcha\Captcha;
use yii\helpers\Url;
use yii\jui\AutoComplete;
use yii\web\JsExpression;

class ActiveField extends \obbz\yii2\widgets\ActiveField
{
    public $options = ['class' => 'form-group fg-float'];
    public $template = "{addonPrepend}\n<div class=\"fg-line\">{label}\n{input}\n{hint}</div>\n{addonAppend}\n{error}"; // for floating label
    public $labelOptions = ['class' => 'fg-label'];

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
    public $checkboxTemplate = "<div class=\"checkbox m-b-15 {disabled}\">\n{beginLabel}\n{input}<i class=\"input-helper\"></i>\n{labelTitle}\n{endLabel}\n{error}\n{hint}\n</div>";
    /**
     * @var string the template for radios in default layout
     */
    public $radioTemplate = "<div class=\"radio m-b-15\">\n{beginLabel}\n{input}<i class=\"input-helper\"></i>\n{labelTitle}\n{endLabel}\n{error}\n{hint}\n</div>";
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
    public $dateTimePickerTemplate = "<div class=\"datetimepicker-widget\">{label}\n{input}\n{hint}\n{error}</div>";
    public $captchaTemplate = "{label}\n<div class=\"row\">{input}</div>\n{hint}\n{error}";
    public $staticControlTemplate = "
                    <dl class=\"dl-horizontal\">
                        <dt>{label}</dt>
                        <dd>{value}</dd>
                    </dl>";

    public $enableFloatLabel = true;

    /**
     * @var array addon options for text and password inputs. The following settings can be configured:
     * - `prepend`: _string_, the prepend addon content
     * - `append`: _array_, the append addon configuration
     * - `asButton`: _boolean_, whether the addon is a button or button group. Defaults to false.
     * - `options`: _array_, the HTML attributes to be added to the container.
     * - `append`: _array_, the append addon configuration
     * - `content`: _string_|_array_, the append addon content
     * - `asButton`: _boolean_, whether the addon is a button or button group. Defaults to false.
     * - `options`: _array_, the HTML attributes to be added to the container.
     * - `groupOptions`: _array_, HTML options for the input group
     * - `contentBefore`: _string_, content placed before addon
     * - `contentAfter`: _string_, content placed after addon
     */
    public $addon = [];

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
            if (isset($this->parts['{value}'])) {
                $this->parts['{value}'] = (isset($this->options['value'])) ? $this->options['value']: $this->model[$this->attribute];
            }

            $this->generateAddon();

        }

        return parent::render($content);
    }

    /**
     * @inheritdoc
     */
    public function dropDownList($items, $options = [])
    {
        $this->disableFloatingLabel();

        if($this->form->layout === 'default'){
            $this->label(true);
            $this->options = ['class' => 'form-group fg-padding'];
        }

        // parent dropDownList

//        $options = array_merge($this->inputOptions, $options);
//        $this->addAriaAttributes($options);
//        $this->adjustLabelFor($options);
//        $this->parts['{input}'] = Html::activeDropDownList($this->model, $this->attribute, $items, $options);

        return '<div class="select">'.parent::dropDownList($items, $options) . '</div>';
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
            if (isset($options['disabled'])) {
                $this->parts['{disabled}'] = "disabled";
                $this->labelOptions['class'] = "disabled";
            }
            else if (isset($options['readonly'])) {
                $this->parts['{disabled}'] = "disabled";
                $this->labelOptions['class'] = "disabled";
            }
            else{
                $this->labelOptions['class'] = null;
                $this->parts['{disabled}'] = "";
            }

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

        $this->template = $this->staticControlTemplate;
//        $this->adjustLabelFor($options);
        $this->parts['{label}'] = $this->model->getAttributeLabel($this->attribute);
        $this->parts['{value}'] = $this->model[$this->attribute];
        return $this;
    }

    public function hiddenInput($options = [])
    {

        $options = array_merge($this->inputOptions, $options);
        $this->template = "{input}";
        $this->options = [];
        $this->adjustLabelFor($options);
        $this->parts['{input}'] = Html::activeHiddenInput($this->model, $this->attribute, $options);

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
        $this->disableFloatingLabel();
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

        $this->disableFloatingLabel();
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
                                <div class="fileinput-preview thumbnail" data-trigger="fileinput">'. Html::img($imgPath) .'</div>
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

    public function disableFloatingLabel(){
        $this->label(false);
        $this->options = ['class' => 'form-group'];

        if($this->form->layout == "inline"){
            $this->inputOptions['placeholder'] = $this->model->getAttributeLabel($this->attribute);
        }

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

    public function widget($class, $config = [], $mdClear = true){
        if($mdClear){
            $this->disableFloatingLabel();
            $this->label(true);
            $this->options = ['class' => 'form-group fg-padding'];
        }

        $this->template = $this->widgetTemplate;
        return parent::widget($class, $config);
    }

    public function datePicker($config=[]){
        $this->template = $this->dateTimePickerTemplate;
        $this->options = ['class' => 'form-group fg-padding'];
        $dateFormat = ObbzYii::formatter()->dateFormat;
        return \yii\bootstrap\ActiveField::widget(DatePicker::className(), array_merge([
            'type' => DatePicker::TYPE_COMPONENT_APPEND,
            'pluginOptions' => [
                'autoclose' => true,
                'format' => ObbzYii::formatter()->convertDateYiiToBsDatepicker($dateFormat),
            ]
        ], $config));
    }

    public function timePicker($config=[]){
        $this->template = $this->dateTimePickerTemplate;
        $this->options = ['class' => 'form-group fg-padding'];
        $dateFormat = ObbzYii::formatter()->timeFormat;
        return \yii\bootstrap\ActiveField::widget(TimePicker::className(), array_merge([
            'name' => $this->attribute,
            'pluginOptions' => [
                'showMeridian' => false,
//                'format' => ObbzYii::formatter()->convertDateYiiToBsDatepicker($dateFormat),
            ]

        ], $config));
    }

    public function dateTimePicker($config=[]){
        $this->template = $this->dateTimePickerTemplate;
        $this->options = ['class' => 'form-group fg-padding'];
        $datetimeFormat = ObbzYii::formatter()->datetimeFormat;
        return \yii\bootstrap\ActiveField::widget(DateTimePicker::className(), array_merge([
            'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
            'pluginOptions' => [
                'autoclose' => true,
                'format' => ObbzYii::formatter()->convertDateYiiToBsDatepicker($datetimeFormat),
            ]
        ], $config));
    }
    public function captcha($config = []){
        $this->template = $this->captchaTemplate;
        $this->enableLabel = false;
        $config['template'] = isset($config['template']) ?
            $config['template'] :
            '<div class="row"><div class="col-sm-3">{image}</div><div class="col-sm-9"><div class="fg-line">{input}</div></div></div>';

        return parent::widget(Captcha::className(), $config);
    }

    public function rte($config = []){

//        $this->disableFloatingLabel();
//        $this->label(true);

        return $this->widget(CoreCKEditor::className(), array_merge([
            'options' => ['rows' => 6],
            'preset' => ObbzYii::user()->can(\backend\components\Roles::THE_CREATOR) ? 'full' : 'basic',
            'clientOptions' => [
                'filebrowserUploadUrl' => Url::to(['/site/ckeditor-upload-img'])
            ]
        ], $config));
    }


    public function autoCompleteWithId($idAttribute, $data, $config = []){

        return \yii\widgets\ActiveField::widget(
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
//        $this->options = ['class' => 'form-group fg-float'];
//        $this->template = $this->widgetTemplate;
        return \yii\widgets\ActiveField::widget(
            AutoCompleteAjax::className(),
            array_merge([
                'idAttribute' => $idAttribute,
                'url' => $url,
                'multiple' => false,
            ], $config)

        );
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

    public static function getAddonContent($addon)
    {
        if (!is_array($addon)) {
            return $addon;
        }
        if (!ArrayHelper::isIndexed($addon)) {
            $addon = [$addon]; //pack existing array into indexed array
        }
        $html = "";
        foreach ($addon as $addonItem) {
            $content = ArrayHelper::getValue($addonItem, 'content', '');
            $options = ArrayHelper::getValue($addonItem, 'options', []);
            $suffix = ArrayHelper::getValue($addonItem, 'asButton', false) ? 'btn' : 'addon';
            Html::addCssClass($options, 'input-group-' . $suffix);
            $html .= Html::tag('span', $content, $options);
        }
        return $html;
    }

    protected function generateAddon()
    {
//        if(!empty($this->addon)){
//            $this->options["class"] .= " input-group fg-float";
//        }
        if(!empty($this->addon['prepend']) || !empty($this->addon['append'])){
            $htmlPrepend = '<div class="input-group fg-float">';
            $htmlAppend = '</div>';
        }else{
            $htmlPrepend = '<div>';
            $htmlAppend = '</div>';
        }

        $this->parts['{addonPrepend}'] = $htmlPrepend. static::getAddonContent(ArrayHelper::getValue($this->addon, 'prepend', '')) ;
        $this->parts['{addonAppend}'] = static::getAddonContent(ArrayHelper::getValue($this->addon, 'append', '')) . $htmlAppend;

    }
}
