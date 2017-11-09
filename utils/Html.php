<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\utils;


class Html
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
}