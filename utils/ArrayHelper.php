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
     * data -   [
     *              ['id'=>1, 'title'=>'a'],
     *              ['id'=>2, 'title'=>'b'],
     *              ['id'=>3, 'title'=>'a'],
     *          ]
     * field - title
     * value - a
     *
     * result - [
     *              ['id'=>1, 'title'=>'a'],
     *              ['id'=>3, 'title'=>'a'],
     *          ]
     *
     * @param ActiveRecord[] $models - array of Model
     * @param string $field - attribute name for search
     * @param int|string $value - value for need to equal
     * @param bool $reIndex - need to re index of result
     * @return ActiveRecord[]
     */
    public static function modelFilterEqual($models, $field, $value, $reIndex = true){
        $result = [];
        foreach($models as $key => $model){
            if(!$model->hasAttribute($field))
                return [];

            if( $model->$field == $value){
                if($reIndex)
                    $result[] = $model;
                else
                    $result[$key] = $model;
            }
        }
        return $result;
    }


    /**
     * @param ActiveRecord[] $models
     * @param int $limit
     * @param string $startTimeField
     * @param string $endTimeField
     * @return ActiveRecord[]
     */
    public static function modelFilterByPeriodTime($models, $limit = null, $startTimeField = 'start_time', $endTimeField = 'end_time'){
        $resultModels = $models;
        $curDate = ObbzYii::formatter()->asDbDatetime();

        foreach($models as $key => $model){
            if(isset($model->$startTimeField) && $model->$startTimeField > $curDate){
                unset($resultModels[$key]);
            }
            if(isset($model->$endTimeField) && $model->$endTimeField < $curDate){
                unset($resultModels[$key]);
            }
        }
        if($limit > 0){
            $resultModels = array_slice($resultModels, 0, $limit);
        }
        return $resultModels;
    }

    /**
     * data -   [
     *              ['id'=>1, 'title'=>'a'],
     *              ['id'=>2, 'title'=>'b'],
     *              ['id'=>3, 'title'=>'c'],
     *          ]
     * field - title
     *
     * result - ['a', 'b', 'c']
     *
     * @param Model[] $data - array of Model
     * @param string $field - attribute name for search
     * @return array of field value
     */
    public static function prepareInQueryArray($data, $field = 'id'){
        $result = [];
        foreach($data as $model){
            $result[] = $model[$field];
        }
        return array_unique($result);
    }

    /**
     * data -   [
     *              ['id'=>1, 'title'=>'a'],
     *              ['id'=>2, 'title'=>'b'],
     *              ['id'=>3, 'title'=>'c'],
     *          ]
     * field - id
     *
     * result - 1,2,3
     *
     * @param Model[] $data - array of Model
     * @param string $field - attribute name for search
     * @return array of field value
     */
    public static function prepareInQueryString($data, $field = 'id'){
        $inQuery = self::prepareInQueryArray($data, $field);
        return implode(',', $inQuery);
    }

    /**
     * - get item number by dataProvider widget
     * @param $widget ListView
     * @param $currentIndex
     * @return mixed
     */
    public static function getItemNumberViaWidget($widget, $currentIndex){
        $totalCount = $widget->dataProvider->getPagination()->totalCount - $widget->dataProvider->getPagination()->getOffset();
        return $totalCount - $currentIndex;
    }

    /**
     * glue - :
     * array - ['1'=>'a', '2'=>'b', '3'=>'c']
     *
     * result - 1:2:3
     *
     * @param $glue
     * @param $array of model for implode
     * @return string
     */
    public static function implodeByKey($glue, array $array){
        $arrayImplode = array_keys($array);
        return implode($glue, $arrayImplode);
    }

    /**
     * models - [
     *              ['id'=>1, 'title'=>'a'],
     *              ['id'=>2, 'title'=>'b'],
     *              ['id'=>3, 'title'=>'c'],
     *          ]
     * field - id
     *
     * result - [
     *              1=>['id'=>1, 'title'=>'a'],
     *              2=>['id'=>2, 'title'=>'b'],
     *              3=>['id'=>3, 'title'=>'c'],
     *          ]
     *
     *
     * @param Model[] $models
     * @param string $field
     * @return Model[]
     */
    public static function indexedModelsByField($models, $field){
        $result = [];
        foreach($models as $model){

            $result[$model->$field] = $model;
        }
        return $result;
    }

    /**
     * convert tag value eg. "abc,cdf,ghi"  to able to using with select items array
     *
     * @param $tagValue
     * @param string $seperator
     * @return array
     */
    public static function tagValue2DropdownItems($tagValue, $tagSeperator = ","){
        $exp = explode($tagSeperator, $tagValue);
        $options = [];
        foreach($exp as $value){
            $options[$value] = $value;
        }
        return $options;
    }

}