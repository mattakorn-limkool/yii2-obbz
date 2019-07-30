<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\utils;

use yii\base\Component;

class YoutubeUtilConfig extends Component
{
    // default config
    public $autoplay = false;
    public $allowFullscreen = true;
    public $allowAutoplay = true;

    // for bootstrap
    const ASPECT_RETIO_21_9 = 'embed-responsive-21by9';
    const ASPECT_RETIO_16_9 = 'embed-responsive-16by9';
    const ASPECT_RETIO_4_3 = 'embed-responsive-4by3';
    const ASPECT_RETIO_1_1 = 'embed-responsive-1by1';

    public $bootstrapContainerClass = 'embed-responsive';
    public $bootstrapContainerRatio = self:: ASPECT_RETIO_16_9;

}

/**
 * Help to easy to embled url youtube
 * Class YoutubeUtil
 * @package obbz\yii2\utils
 */
class YoutubeUtil
{
    const EMBLED_URL_PREFIX = 'https://www.youtube.com/embed/';

    const THUMB_QUALITY_LOW = 'sddefault.jpg';
    const THUMB_QUALITY_MEDIUM = 'mqdefault.jpg';
    const THUMB_QUALITY_HIGH = 'hqdefault.jpg';
    const THUMB_QUALITY_MAX = 'maxresdefault.jpg';



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
        return isset($match[1]) ? $match[1] : '';
    }


    /**
     * using for embed VDO to iframe
     * @param $youtubeLink
     * @param YoutubeUtilConfig $config
     * @return string
     */
    public static function getEmbedUrl($youtubeLink, $config = null){
        $suffix = '';
        $config  = self::prepareConfig($config);
        if($config->autoplay){
            $suffix = '?autoplay=1';
        }

        $id = self::getIdByUrl($youtubeLink);
        return self::EMBLED_URL_PREFIX . $id . $suffix;
    }

    /**
     * @param $youtubeLink
     * @param YoutubeUtilConfig $config
     * @return string
     */
    public static function getEmbedCode($youtubeLink, $config = null){
        $embedUrl = self::getEmbedUrl($youtubeLink);
        $config  = self::prepareConfig($config);
        $iframeAttr = '';
        if($config->allowFullscreen){
            $iframeAttr = 'allowfullscreen';
        }

        if($config->allowAutoplay){
            $iframeAttr .= ' allow="autoplay"';
        }

        $result = '<iframe class="embed-responsive-item" src="'. $embedUrl .'" '. $iframeAttr .'></iframe>';
        return $result;
    }

    /**
     * get youtube embed code reponsive support by bootstrap3 (not test on v4 yet)
     * @param $youtubeLink
     * @param array $bootstrapConfig
     * @param array $youtubeConfig
     * @return string
     */
    public static function getBootstrapEmbedCode($youtubeLink, $config = null){
        $config  = self::prepareConfig($config);
        $iframe = self::getEmbedCode($youtubeLink, $config);

        $containerClass = $config->bootstrapContainerClass . ' ' . $config->bootstrapContainerRatio;

        return '<div class="'. $containerClass .'">'. $iframe .'</div>';
    }

    /**
     * @param null $config
     * @return YoutubeUtilConfig
     */
    public static function prepareConfig($config = null){
        if(!isset($config)){
            $config = new YoutubeUtilConfig();
        }
        if($config->autoplay){
            $config->allow_autoplay = true;
        }

        return $config;
    }


    /** for youtube thumbnail */
    public static function getDefaultThumbnail($youtubeLink, $quality = self::THUMB_QUALITY_MEDIUM){
        $id = self::getIdByUrl($youtubeLink);
        return "http://img.youtube.com/vi/{$id}/{$quality}";
    }


    /**
     * @deprecated
     * @param $config
     * @return string
     */
    public static function parseConfig2String($config){
        $result = '';
        foreach($config as $key => $value){
            if(is_numeric($key)){
                $result .= " " . $value;
            }else{
                $result .= " ".$key . ' = "'. $value .'"';
            }

        }
        return $result;
    }

}