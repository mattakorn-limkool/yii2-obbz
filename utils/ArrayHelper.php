<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\utils;


class ArrayHelper extends \yii\helpers\ArrayHelper
{
    /**
     * @param Model[] $data - array of Model
     * @param string $field - attribute name for search
     * @param int|string $value - value for need to equal
     * @return array of Model
     */
    public static function modelFilterEqual($data, $field, $value){
        $result = [];
        foreach($data as $model){
            if($model->$field == $value){
                $result[] = $model;
            }
        }
        return $result;
    }
}