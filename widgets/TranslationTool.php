<?php
namespace obbz\yii2\widgets;

use \Yii;

class TranslationTool extends \yii\base\Widget
{
    public $model;

    public function run(){
        if(empty($this->model)){
            throw new \Exception('Please define model before');
            return;
        }

        if(!empty(\Yii::$app->params['languages'])){
            $translateLanguages = \Yii::$app->params['languages'];
            // remove default language
            unset($translateLanguages[\Yii::$app->params['defaultLanguage']]);
        }

        if($this->model->getBehavior('translateable')){
            $result = '
            <div class="btn-group">
                <button type="button" class="btn btn-default dropdown-toggle waves-effect"
                    data-toggle="dropdown" aria-expanded="false">
                    <i class="zmdi zmdi-refresh"></i> Translate to</button><ul class="dropdown-menu" role="menu">';

                foreach($translateLanguages as $key=>$value){
                    $result .= '<li><a
                   href="'. \yii\helpers\Url::to(["translate", 'id'=>$this->model->id, 'language'=>$key]) .'"
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

        return '';
    }

}
