<?php
namespace obbz\yii2\widgets;

use \Yii;

class SummaryArea extends \yii\bootstrap\Widget
{
    public $areaSelector;
    public $inputTotal;

    public function run()
    {
        if(empty($this->areaSelector)){
            throw new Exception('Please define areaSelector before');
            return;
        }
        if(empty($this->inputTotal)){
            throw new Exception('Please define areaSelector before');
            return;
        }

        $widgetId = $this->getId();
        $this->getView()->registerJs( <<<JS
            var summaryArea{$widgetId} = {
                onChangeArea: function(){
                    console.log(55);
                    var sumArray = [];
                    var total = 0;
                    $("{$this->areaSelector} :input").each(function(key){
                        sumArray[key] = parseInt($(this).val()) || 0;
                        total += sumArray[key];
                    });
                    $("{$this->inputTotal}").val(total).closest('.fg-line').addClass('fg-toggled');
                }
            };

JS
            , \yii\web\View::POS_HEAD);

        $this->getView()->registerJs( <<<JS
            summaryArea{$widgetId}.onChangeArea();
            $("{$this->areaSelector} :input").each(function(key){
                $(this).bind('change', function(){
                    summaryArea{$widgetId}.onChangeArea();
                });
            });
JS
            , \yii\web\View::POS_READY);
    }
}
