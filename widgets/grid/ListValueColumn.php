<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\widgets\grid;


use yii\base\Exception;
use yii\grid\DataColumn;

class ListValueColumn extends DataColumn
{
    public $callClass = null;
    public $callFunc = null;
    public $callArgs = [];
    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        if(!isset($this->callFunc)){
            throw new Exception('Please provide callFunc on ListValueColumn');
        }else{
            if(!isset($this->callClass)){
                return call_user_func_array([$model, $this->callFunc], $this->callArgs);
            }else{
                return call_user_func_array([$this->callClass, $this->callFunc], $this->callArgs);
            }
        }

    }
}