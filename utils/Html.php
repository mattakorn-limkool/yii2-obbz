<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\utils;


class Html
{
    public static function getSrcFromTag($tag){
        preg_match('/src="([^"]+)"/', $tag, $match);
        return $url = $match[1];
    }
}