<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\utils;

/**
 * Help to easy to embled url youtube
 * Class YoutubeUtil
 * @package obbz\yii2\utils
 */
class YoutubeUtil
{
    const EMBLED_URL_PREFIX = 'https://www.youtube.com/embed/';

    // for bootstrap
    const ASPECT_RETIO_21_9 = 'embed-responsive-21by9';
    const ASPECT_RETIO_16_9 = 'embed-responsive-16by9';
    const ASPECT_RETIO_4_3 = 'embed-responsive-4by3';
    const ASPECT_RETIO_1_1 = 'embed-responsive-1by1';
    /**
     * Get only Id from youtube
     *
    // http://youtu.be/dQw4w9WgXcQ
    // http://www.youtube.com/embed/dQw4w9WgXcQ
    // http://www.youtube.com/watch?v=dQw4w9WgXcQ
    // http://www.youtube.com/?v=dQw4w9WgXcQ
    // http://www.youtube.com/v/dQw4w9WgXcQ
    // http://www.youtube.com/e/dQw4w9WgXcQ
    // http://www.youtube.com/user/username#p/u/11/dQw4w9WgXcQ
    // http://www.youtube.com/sandalsResorts#p/c/54B8C800269D7C1B/0/dQw4w9WgXcQ
    // http://www.youtube.com/watch?feature=player_embedded&v=dQw4w9WgXcQ
    // http://www.youtube.com/?feature=player_embedded&v=dQw4w9WgXcQ
     *
     * @param $youtubeLink
     * @return mixed
     */
    public static function getIdByUrl($youtubeLink){
        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $youtubeLink, $match);
        return $match[1];
    }

    /**
     * using for embed VDO to iframe
     * @param $youtubeLink
     * @return string
     */
    public static function getEmbedUrl($youtubeLink){
        $id = self::getIdByUrl($youtubeLink);
        return self::EMBLED_URL_PREFIX . $id;
    }

    /**
     * @param $youtubeLink
     * @param array $config
     * @return string
     */
    public static function getEmbedCode($youtubeLink, $config = ['allowfullscreen']){
        $embedUrl = self::getEmbedUrl($youtubeLink);
        $confStr = self::parseConfig2String($config);
        $result = '<iframe class="embed-responsive-item" src="'. $embedUrl .'" '. $confStr .'></iframe>';
        return $result;
    }

    /**
     * get youtube embed code reponsive support by bootstrap3 (not test on v4 yet)
     * @param $youtubeLink
     * @param array $bootstrapConfig
     * @param array $youtubeConfig
     * @return string
     */
    public static function getBootstrapEmbedCode($youtubeLink, $bootstrapConfig = ['embed-responsive-16by9'], $youtubeConfig = ['allowfullscreen']){
        $iframe = self::getEmbedCode($youtubeLink, $youtubeConfig);
        $confStr = self::parseConfig2String($bootstrapConfig);
        return '<div class="embed-responsive '. $confStr .'">'. $iframe .'</div>';
    }


    public static function parseConfig2String($config){
        $result = '';
        foreach($config as $key => $value){
            $result .= " " . $value;
        }
        return $result;
    }
}