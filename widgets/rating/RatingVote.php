<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\widgets\rating;

use obbz\yii2\behaviors\RatingBehavior;
use obbz\yii2\models\CoreActiveRecord;
use obbz\yii2\utils\ObbzYii;
use yii\base\Widget;
use yii\helpers\Url;
use yii\web\JsExpression;

class RatingVote extends Rating
{
    public $viewFile = '@vendor/obbz/yii2/widgets/rating/views/vote';
    public $ajaxSubmit = true;
    public $ajaxSubmitUrl = ['site/rating'];
    public $jsBeforeSendRequest = 'function( xhr ) {}';
    public $jsDoneSendRequest = "function(data){}";

    public function run()
    {

        return $this->render($this->viewFile, [
            'pluginOptions'=>array_merge($this->getDefaultPluginOptions(), $this->pluginOptions),
            'pluginEvents'=>array_merge($this->getDefaultPluginEvents(), $this->pluginEvents),
            'userValue' =>$this->userValue,
        ]);
    }

    public function getDefaultPluginOptions(){
        return [
            'theme' => 'krajee-fa', // 'krajee-svg', 'krajee-uni', 'krajee-fa'
            'size' => 'sm',
            'showCaption'=>false,
            'step' => $this->stepValue,
            'min' => $this->minValue,
            'max' => $this->maxValue,
            'stars' => $this->stars,
        ];
    }

    public function getDefaultPluginEvents(){
        if($this->ajaxSubmitUrl){
            $modelPk = $this->model->getPrimaryKey();
            $ajaxSubmitUrl = Url::to($this->ajaxSubmitUrl + ['entity'=>$this->entity, 'target_id'=>$modelPk, 'version'=>$this->version]);
            $submitJs = new JsExpression("function(event, starValue) {

                jQuery.ajax({
                    url: '$ajaxSubmitUrl&action=' + event.type + '&value=' + starValue, type: 'POST', dataType: 'json', cache: false,
                    beforeSend: $this->jsBeforeSendRequest
                }).done($this->jsDoneSendRequest);
            }");

            return $pluginEvents = [
                "rating:change" => $submitJs,
                "rating:clear" => $submitJs,
                "rating:reset" => $submitJs,
            ];
        }else{
            return [];
        }

    }
}