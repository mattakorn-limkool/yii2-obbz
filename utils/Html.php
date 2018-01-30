<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\utils;


use yii\helpers\ArrayHelper;

class Html extends \yii\helpers\Html
{
    /**
     * get src value from a html tag or other html tag
     * @param $tag
     * @return mixed
     */
    public static function getSrcFromTag($tag){
        preg_match('/src="([^"]+)"/', $tag, $match);
        return $url = $match[1];
    }

    /**
     * convert html to inline html string
     *  benefit - using for convert html php template to store js variable
     * @param $htmlString
     * @return string
     */
    public static function toInline($htmlString){
        $result = preg_replace('/>\s+</', '><', $htmlString);
        $result = str_replace(["\n", "\r", "\t"], "", $result);
        return $result;
    }

    /**
     * @param $valueKey - value key of list
     * @param $mappingList - mapping of list
     * @param $mapingConfig  - array for mapping between value list key and css class
     *                  [
     *                       'success_key' => [
     *                                          'css'  => 'text-error',
     *                                          'icon' => 'fa fa-close',
     *                                       ]
     *                          ]
     * @param $wrapper
     */
    public static function wrapListValue(
                     $valueKey, $mappingList,
                     $mappingConfig = [],
                     $wrapper = '<span class="{css}">{label}</span>',
                     $defaultValue='')
    {
        $label = ArrayHelper::getValue($mappingList, $valueKey);
        if($label != null){
            $result = $wrapper;
            $css = ArrayHelper::getValue($mappingConfig, $valueKey . '.css', '');
            $result = str_replace('{css}', $css, $result);
            $icon = ArrayHelper::getValue($mappingConfig, $valueKey . '.icon', '');
            $result = str_replace('{icon}', $icon, $result);
            $result = str_replace('{label}', $label, $result);
            return $result;
        }else{
            return $defaultValue;
        }
    }

    public static function socialUrl($username, $url = '', $urlEmptyName = 'Url'){
        if(empty($url) && empty($username)){
            return '';
        }
        else if(empty($url)){
            return $username;
        }
        else if(empty($username)){
            return self::a($urlEmptyName, $url, ['target'=>'_blank']);
        }
        else{
            return self::a($username, $url, ['target'=>'_blank']);
        }
    }
}