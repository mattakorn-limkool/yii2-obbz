<?php
namespace obbz\yii2\widgets;

use Illuminate\Support\Arr;
use obbz\yii2\utils\ArrayHelper;
use obbz\yii2\utils\ObbzYii;
use \Yii;

class TranslationTool extends \yii\base\Widget
{
    public $model;
    public $translateLanguages;

    // todo - showTranslatedIcon
    public $showTranslatedIcon = true;

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
            unset($this->_languages[\Yii::$app->params['defaultLanguage']]);

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

                foreach($this->_languages as $key=>$value){
                    $result .= '<li><a
                   href="'. \yii\helpers\Url::to(["translate", 'id'=>$this->model->id, 'language'=>$key, 'key'=>$key_name]) .'"
                   class="translate-btn">'. $value .'</a></li>';
                }

                $result .= '</ul></div>';


                $result .= \newerton\fancybox\FancyBox::widget([
                    'target' => 'a.translate-btn',
                    'config'=>[
                        'type'=>'iframe',
                        'width' => '80%',
                    ]
                ]);
                return $result;
            }

        }

        return '';
    }

//    public function translationLanguages(){
//        return ArrayHelper::getValue( \Yii::$app->params, 'languages', []);
//    }

}
