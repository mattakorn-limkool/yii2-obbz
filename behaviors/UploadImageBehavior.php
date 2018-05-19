<?php

namespace obbz\yii2\behaviors;

use Imagine\Image\ManipulatorInterface;
use obbz\yii2\utils\ObbzYii;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\db\BaseActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\imagine\Image;

class UploadImageBehavior extends UploadBehavior
{
    /**
     * @var string|array|Closure align for default thumbnal if closure must be return align path for default thaumbnail img
     *
     * ```php
     * function ($model, $behavior)
     * ```
     *
     * - `$model`: the current model
     * - `$behavior`: the current UploadImageBehavior instance
     *
     */
    public $placeholder;
    /**
     * @var boolean
     */
    public $createThumbsOnSave = true;
    /**
     * @var boolean
     */
    public $createThumbsOnRequest = false;
    /**
     * @var array the thumbnail profiles
     * - `width`
     * - `height`
     * - `quality`
     */
    public $thumbs = [
        'thumb' => ['width' => 200, 'height' => 200, 'quality' => 100],
    ];
    /**
     * @var string|null
     */
    public $thumbPath;
    /**
     * @var string|null
     */
    public $thumbUrl;

    /**
     * work with createThumbsOnSave only
     * @var bool
     */
    public $removeOriginalImage = false;


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if ($this->createThumbsOnSave) {
            if ($this->thumbPath === null) {
                $this->thumbPath = $this->path;
            }
            if ($this->thumbUrl === null) {
                $this->thumbUrl = $this->url;
            }

            foreach ($this->thumbs as $config) {
                $width = ArrayHelper::getValue($config, 'width');
                $height = ArrayHelper::getValue($config, 'height');
                if ($height < 1 && $width < 1) {
                    throw new InvalidConfigException(sprintf(
                        'Length of either side of thumb cannot be 0 or negative, current size ' .
                            'is %sx%s', $width, $height
                    ));
                }
            }
        }
    }

    protected function generateFileName($file)
    {
        return uniqid() . '-' . $this->attribute . '.' . $file->extension;
    }

    /**
     * @inheritdoc
     */
    protected function afterUpload()
    {
        parent::afterUpload();
        if ($this->createThumbsOnSave) {
            $this->createThumbs();
            if($this->removeOriginalImage){
                $path = $this->getUploadPath($this->attribute);
                unlink($path);
            }
        }
    }

    /**
     * @throws \yii\base\InvalidParamException
     */
    protected function createThumbs()
    {
        $path = $this->getUploadPath($this->attribute);
        foreach ($this->thumbs as $profile => $config) {
            $thumbPath = $this->getThumbUploadPath($this->attribute, $profile);
            if ($thumbPath !== null) {
                if (!FileHelper::createDirectory(dirname($thumbPath))) {
                    throw new InvalidParamException("Directory specified in 'thumbPath' attribute doesn't exist or cannot be created.");
                }
                if (!is_file($thumbPath)) {
                    $this->generateImageThumb($config, $path, $thumbPath);
                }
            }
        }
    }

    /**
     * @param string $attribute
     * @param string $profile
     * @param boolean $old
     * @return string
     */
    public function getThumbUploadPath($attribute, $profile = 'thumb', $old = false)
    {
        /** @var BaseActiveRecord $model */
        $model = $this->owner;
        $path = $this->resolvePath($this->thumbPath);
        $attribute = ($old === true) ? $model->getOldAttribute($attribute) : $model->$attribute;
        $filename = $this->getThumbFileName($attribute, $profile);

        return $filename ? Yii::getAlias($path . '/' . $filename) : null;
    }

    /**
     * @param string $attribute
     * @param string $profile
     * @return string|null
     */
    public function getThumbUploadUrl($attribute, $profile = 'thumb')
    {
        /** @var BaseActiveRecord $model */
        $model = $this->owner;
        if($this->createThumbsOnRequest){
            $path = $this->getUploadPath($attribute, true);
        }else{
            $path = $this->getThumbUploadPath($attribute, $profile, true);
        }


        if (is_file($path)) {
            if ($this->createThumbsOnRequest) {
                $this->createThumbs();
            }
            $url = $this->resolvePath($this->thumbUrl);
            $fileName = $model->getOldAttribute($attribute);
            $thumbName = $this->getThumbFileName($fileName, $profile);
            $abc = Yii::getAlias($url );
            return Yii::getAlias($url . '/' . $thumbName);
        } elseif ($this->placeholder) {
            return $this->getPlaceholderUrl($profile);
        } else {
            return null;
        }
    }

    /**
     * @param $profile
     * @return string
     */
    protected function getPlaceholderUrl($profile)
    {
        if ($this->placeholder instanceof \Closure) {
            $placeholderPath = call_user_func($this->placeholder, $this->owner, $this);
        }else if(is_array($this->placeholder)){
            $placeholderPath = call_user_func($this->placeholder);
        }
        else { // string
            $placeholderPath = $this->placeholder;
        }

        $thumbUrl =  str_replace("@uploadPath", "@uploadUrl",  $placeholderPath);

        return Yii::getAlias($thumbUrl);
    }
//    protected function getPlaceholderUrlBAK($profile)
//    {
//        if ($this->placeholder instanceof \Closure) {
//            $placeholderPath = call_user_func($this->placeholder, $this->owner, $this);
//        }else if(is_array($this->placeholder)){
//            $placeholderPath = call_user_func($this->placeholder);
//        }
//        else { // string
//            $placeholderPath = $this->placeholder;
//        }
//
//        list ($path, $url) = Yii::$app->assetManager->publish($placeholderPath);
//        $filename = basename($path);
//        $thumb = $this->getThumbFileName($filename, $profile);
//        $thumbPath = dirname($path) . DIRECTORY_SEPARATOR . $thumb;
////        $thumbUrl =  dirname($url) . '/' . $thumb;
//        $chkAssets = explode("/", $url);
//        $foundAssetKey = array_search("assets", $chkAssets);
//        if($foundAssetKey !== false){
//            foreach($chkAssets as $key => $chkAsset){
//                if($foundAssetKey > $key)
//                    unset($chkAssets[$key]);
//            }
//            $url = '@frontendUrl/' . implode('/', $chkAssets);
//        }else{
//            $url = dirname($url) . '/' . $thumb;
//        }
//
//        $thumbUrl =  $url;
//
//        if (!is_file($thumbPath)) {
//            $this->generateImageThumb($this->thumbs[$profile], $path, $thumbPath);
//        }
//
//        return Yii::getAlias($thumbUrl);
//    }

    /**
     * @inheritdoc
     */
    protected function delete($attribute, $old = false)
    {
        parent::delete($attribute, $old);

        $profiles = array_keys($this->thumbs);
        foreach ($profiles as $profile) {
            $path = $this->getThumbUploadPath($attribute, $profile, $old);
            if (is_file($path)) {
                unlink($path);
            }
        }
    }

    /**
     * @param $filename
     * @param string $profile
     * @return string
     */
    protected function getThumbFileName($filename, $profile = 'thumb')
    {
        return $profile . '-' . $filename;
    }

    /**
     * @param $config
     * @param $path
     * @param $thumbPath
     */
    protected function generateImageThumb($config, $path, $thumbPath)
    {
        $width = ArrayHelper::getValue($config, 'width');
        $height = ArrayHelper::getValue($config, 'height');
        $quality = ArrayHelper::getValue($config, 'quality', 100);
        $mode = ArrayHelper::getValue($config, 'mode', ManipulatorInterface::THUMBNAIL_OUTBOUND);

        if (!$width || !$height) {
            $image = Image::getImagine()->open($path);
            $ratio = $image->getSize()->getWidth() / $image->getSize()->getHeight();
            if ($width) {
                $height = ceil($width / $ratio);
            } else {
                $width = ceil($height * $ratio);
            }
        }
        // Fix error "PHP GD Allowed memory size exhausted".
        $defaulMemLimit = ini_get ('memory_limit');
        ini_set('memory_limit', '512M');
        Image::thumbnail($path, $width, $height, $mode)->save($thumbPath, ['quality' => $quality]);
        ini_set ('memory_limit',$defaulMemLimit);
    }
}
