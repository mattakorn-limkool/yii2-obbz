<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\widgets;


use yii\helpers\Html;
use yii\jui\AutoComplete;
use yii\web\JsExpression;

class AutoCompleteWithId extends AutoComplete
{
    public $idAttribute = '';

    public function run()
    {
        if(empty($this->idAttribute)){
            throw new Exception('Please define idAttribute before');
            return;
        }

        $inputId = \yii\helpers\Html::getInputId($this->model, $this->attribute);
        $hiddenInputId = \yii\helpers\Html::getInputId($this->model, $this->idAttribute);


        if(!isset($this->clientOptions['select'])){
            $this->clientOptions['select'] = new JsExpression("function( event, ui ) {
                    $('#{$hiddenInputId}').val(ui.item.id);
                 }");
        }
        $this->getView()->registerJs("
         $('#{$inputId}').keyup(function(event) {
                    $('#{$hiddenInputId}').val('');
         });
        ");

        return parent::run() .
        Html::activeHiddenInput($this->model, $this->idAttribute, ['id' => $hiddenInputId, 'class' => 'form-control'])
            ;

    }
}