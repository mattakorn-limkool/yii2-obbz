<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\widgets;


use yii\base\Exception;
use yii\helpers\Html;

class AutoCompleteAjax extends \keygenqt\autocompleteAjax\AutocompleteAjax
{
    public $idAttribute = '';
    public $textInputOptions = [];

    // modify run that's support validation and text input
    public function run()
    {
        if(empty($this->idAttribute)){
            throw new Exception('Please define idAttribute before');
            return;
        }

        $widgetId = $this->getId();
        $id = Html::getInputId($this->model, $this->attribute);
        $this->afterSelect = "var afterSelect{$widgetId} = " . $this->afterSelect;
        $value = $this->model->{$this->idAttribute};
        $this->registerActiveAssets();

        $this->getView()->registerJs("{$this->afterSelect}");

        if ($this->multiple) {

            $this->getView()->registerJs("

                $('#{$id}').keyup(function(event) {

                    if (event.keyCode == 8 && !$('#{$id}').val().length) {

                        $('#{$id}-hidden').val('');
                        $('#{$id}-hidden').change();

                    } else if ($('.ui-autocomplete').css('display') == 'none' &&
                        $('#{$id}-hidden').val().split(', ').length > $(this).val().split(', ').length) {

                        var val = $('#{$id}').val().split(', ');
                        var ids = [];
                        for (var i = 0; i<val.length; i++) {
                            val[i] = val[i].replace(',', '').trim();
                            ids[i] = cache_{$widgetId}_1[val[i]];
                        }
                        $('#{$id}-hidden').val(ids.join(', '));
                        $('#{$id}-hidden').change();
                    }
                });

                $('#{$id}').keydown(function(event) {

                    if (event.keyCode == 13 && $('.ui-autocomplete').css('display') == 'none') {
                        submit_{$widgetId} = $('#{$id}').closest('.grid-view');
                        $('#{$id}').closest('.grid-view').yiiGridView('applyFilter');
                    }

                    if (event.keyCode == 13) {
                        $('.ui-autocomplete').hide();
                    }

                });

                $('body').on('beforeFilter', '#' + $('#{$id}').closest('.grid-view').attr('id') , function(event) {
                    return submit_{$widgetId};
                });

                var submit_{$widgetId} = false;
                var cache_{$widgetId} = {};
                var cache_{$widgetId}_1 = {};
                var cache_{$widgetId}_2 = {};
                jQuery('#{$id}').autocomplete(
                {
                    minLength: 1,
                    source: function( request, response )
                    {
                        var term = request.term;

                        if (term in cache_{$widgetId}) {
                            response( cache_{$widgetId}[term]);
                            return;
                        }
                        $.getJSON('{$this->getUrl()}', request, function( data, status, xhr ) {
                            cache_{$widgetId} [term] = data;

                            for (var i = 0; i<data.length; i++) {
                                if (!(data[i].id in cache_{$widgetId}_2)) {
                                    cache_{$widgetId}_1[data[i].label] = data[i].id;
                                    cache_{$widgetId}_2[data[i].id] = data[i].label;
                                }
                            }

                            response(data);
                        });
                    },
                    select: function(event, ui)
                    {
                        var val = $('#{$id}-hidden').val().split(', ');

                        if (val[0] == '') {
                            val[0] = ui.item.id;
                        } else {
                            val[val.length] = ui.item.id;
                        }

                        $('#{$id}-hidden').val(val.join(', '));
                        $('#{$id}-hidden').change();

                        var names = [];
                        for (var i = 0; i<val.length; i++) {
                            names[i] = cache_{$widgetId}_2[val[i]];
                        }

                        setTimeout(function() {
                            $('#{$id}').val(names.join(', '));
                        }, 0);
                    }
                });
            ");
        } else {
            $this->getView()->registerJs("
                var cache_{$widgetId} = {};
                var cache_{$widgetId}_1 = {};
                var cache_{$widgetId}_2 = {};
                $('#{$id}').keyup(function(event) {
                    $('#{$id}-hidden').val('');
                });
                jQuery('#{$id}').autocomplete(
                {
                    minLength: 1,
                    source: function( request, response )
                    {
                        var term = request.term;
                        if ( term in cache_{$widgetId} ) {
                            response( cache_{$widgetId} [term] );
                            return;
                        }
                        $.getJSON('{$this->getUrl()}', request, function( data, status, xhr ) {
                            cache_{$widgetId} [term] = data;
                            response(data);
                        });
                    },
                    select: function(event, ui)
                    {

                    // console.log(ui);

                        afterSelect{$widgetId}(event, ui);

                        $('#{$id}-hidden').val(ui.item.id);
                         $('#{$id}-hidden').change();
                    }
                });
            ");
        }

        if ($value && $this->startQuery) {
            $this->getView()->registerJs("
                $(function(){
                    $.ajax({
                        type: 'GET',
                        dataType: 'json',
                        url: '{$this->getUrl()}',
                        data: {term: '$value'},
                        success: function(data) {

                            if (data.length == 0) {
                                $('#{$id}').attr('placeholder', 'User not found !!!');
                            } else {
                                var arr = [];
                                for (var i = 0; i<data.length; i++) {
                                    arr[i] = data[i].label;
                                    if (!(data[i].id in cache_{$id}_2)) {
                                        cache_{$widgetId}_1[data[i].label] = data[i].id;
                                        cache_{$widgetId}_2[data[i].id] = data[i].label;
                                    }
                                }
                                $('#{$id}').val(arr.join(', '));
                            }
                            $('.autocomplete-image-load').hide();
                        }
                    });
                });
            ");
        }


        return Html::tag('div',
            // change position for order input
//             Html::activeTextInput($this->model, $this->attribute && !$this->startQuery ? $this->attribute : '', array_merge($this->textInputOptions, ['id' => $id, 'class' => 'form-control']))
             Html::activeTextInput($this->model, $this->attribute, array_merge($this->textInputOptions, ['id' => $id, 'class' => 'form-control']))
            . ($value && $this->startQuery ? Html::tag('div', "<img src='{$this->registerActiveAssets()}/images/load.gif'/>", ['class' => 'autocomplete-image-load']) : '')
            . Html::activeHiddenInput($this->model, $this->idAttribute, ['id' => $id . '-hidden', 'class' => 'form-control'])

            , [
                'style' => 'position: relative;'
            ]
        );
    }
}