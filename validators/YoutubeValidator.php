<?php


namespace obbz\yii2\validators;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Json;
use yii\validators\RegularExpressionValidator;
use yii\web\JsExpression;


class YoutubeValidator extends RegularExpressionValidator
{
    public $pattern = '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i';

}
