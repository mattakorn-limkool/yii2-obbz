<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\utils;


use yii\base\Model;
use yii\widgets\ListView;

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

    /**
     * @param Model[] $data - array of Model
     * @param string $field - attribute name for search
     * @return array of field value
     */
    public static function prepareInQueryArray($data, $field){
        $result = [];
        foreach($data as $model){
            $result[] = $model->$field;
        }
        return $result;
    }

    /**
     * @param $widget ListView
     * @param $currentIndex
     * @return mixed
     */
    public static function getItemNumberViaWidget($widget, $currentIndex){
        $totalCount = $widget->dataProvider->getPagination()->totalCount - $widget->dataProvider->getPagination()->getOffset();
        return $totalCount - $currentIndex;
    }

    /**
     * @param $glue
     * @param $array of model for implode
     * @return string
     */
    public static function implodeByKey($glue, array $array){
        $arrayImplode = array_keys($array);
        return implode($glue, $arrayImplode);
    }

}