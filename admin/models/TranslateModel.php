<?php

namespace obbz\yii2\admin\models;
use yii\base\Model;
use yii\i18n\DbMessageSource;
use yii\i18n\PhpMessageSource;

/**
 * @author  Mattakorn Limkool <obbz.dev@gmail.com>
 *
 */
class TranslateModel extends Model
{
    public $defaultMessage;
    public $translateMessage;

    public function rules(){
        return [
            ['translateMessage', 'safe']
        ];
    }


//    abstract public function

//    public static function getModelByKey($conf, $key){
//        $class = ArrayHelper::getValue($conf, $key . '.class');
//        $basePhpClass = new PhpMessageSource();
//        $baseDbClass = new DbMessageSource();
//        $keyClass = new $class;
//
//        if($class instanceof $basePhpClass){
//            return PhpTranslateModel::class;
//        }elseif($class instanceof $baseDbClass){
//            return DbTranslateModel::class;
//        }else{
//            throw new \ErrorException('Wrong  MessageSource instance please see main app config');
//        }
//    }

    public static function getModels($messages){
        $models = [];
        foreach($messages as $default => $translate){
            $model = new self();
            $model->defaultMessage = $default;
            $model->translateMessage = $translate;
            $models[] = $model;
        }
        return $models;
    }


}