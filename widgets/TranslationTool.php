<?php
namespace obbz\yii2\widgets;

use Illuminate\Support\Arr;
use obbz\yii2\utils\ArrayHelper;
use obbz\yii2\utils\Html;
use obbz\yii2\utils\ObbzYii;
use yii\helpers\Url;
use \Yii;

class TranslationTool extends \yii\base\Widget
{
    public $model;
    public $translateLanguages;

    public $showTranslatedFlag = false;
    public $transatedFlagTemplate = '<i class="empty-translate fa fa-exclamation"></i>';
    public $fancyConfig = [];

    private $_languages;

    public function run(){
        if(empty($this->model)){
            throw new \Exception('Please define model before');
            return;
        }

        if($this->translateLanguages && is_array($this->translateLanguages) && isset($this->translateLanguages[0])){
            $callable = ArrayHelper::getValue($this->translateLanguages, '0');
            $args = ArrayHelper::getValue($this->translateLanguages, '1', []);
            $this->_languages = call_user_func_array($callable, $args);
        }else{

            $this->_languages = ArrayHelper::getValue( \Yii::$app->params, 'languages', []);
            $defaultLanguage = ArrayHelper::getValue(\Yii::$app->params, 'defaultLanguage');
            unset($this->_languages[$defaultLanguage]);
        }


//        $translateLanguages = $this->translationLanguages();
//        // remove default language
//        unset($translateLanguages[\Yii::$app->params['defaultLanguage']]);


        if($translateable = $this->model->getBehavior('translateable')){
            $translateAttrs = ArrayHelper::getValue($translateable, 'translationAttributes');
            if($this->_languages && !empty($translateAttrs)){
                $key_name = ArrayHelper::getValue($this->model, 'key_name');
                $result = '
                        <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle waves-effect"
                                data-toggle="dropdown" aria-expanded="false">
                                <i class="zmdi zmdi-refresh"></i> Translate to</button><ul class="dropdown-menu" role="menu">';
                $text = '';
                foreach($this->_languages as $key=>$value){
                    $text = $value;
                    if($this->showTranslatedFlag && !$this->hasTranslation($key)){
                        $text = $this->transatedFlagTemplate . $text;
                    }

                    $result .= Html::tag('li',
                        Html::a( $text,
                            Url::to(["translate", 'id'=>$this->model->id, 'language'=>$key, 'key'=>$key_name]),
                            ['class'=>'translate-btn']
                        )
                    );
                }

                $result .= '</ul></div>';


                $result .= \newerton\fancybox\FancyBox::widget([
                    'target' => 'a.translate-btn',
                    'config'=>ArrayHelper::merge([
                        'type'=>'iframe',
                        'width' => '80%',
                    ], $this->fancyConfig)
                ]);
                return $result;
            }

        }

        return '';
    }

    public function hasTranslation($lang){
        return $this->model->hasTranslation($lang);
    }
//    public function translationLanguages(){
//        return ArrayHelper::getValue( \Yii::$app->params, 'languages', []);
//    }

}
