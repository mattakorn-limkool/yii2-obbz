<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\behaviors;


use Imagine\Image\ManipulatorInterface;
use yii\db\BaseActiveRecord;
use yii\helpers\ArrayHelper;
use yii\imagine\Image;

class UploadImageBehaviorBAK extends  \mongosoft\file\UploadImageBehavior
{
    /**
     * Deletes old file.
     * @param string $attribute
     * @param boolean $old
     */
    protected function delete($attribute, $old = false)
    {
        $path = $this->getUploadPath($attribute, $old);
        $path = strval(str_replace("\0", "", $path));
        if (@is_file($path)) {
            unlink($path);
        }
    }

    /**
     * Returns file path for the attribute.
     * @param string $attribute
     * @param boolean $old
     * @return string|null the file path.
     */
    public function getUploadPath($attribute, $old = false)
    {
        /** @var BaseActiveRecord $model */
        $model = $this->owner;
        $path = $this->resolvePath($this->path);
//        $path = strval(str_replace("\0", "", $path));
        $fileName = ($old === true) ? $model->getOldAttribute($attribute) : $model->$attribute;

        $fileName ? \Yii::getAlias($path . '/' . $fileName) : null;
    }

//    protected function generateImageThumb($config, $path, $thumbPath)
//    {
//        $width = ArrayHelper::getValue($config, 'width');
//        $height = ArrayHelper::getValue($config, 'height');
//        $quality = ArrayHelper::getValue($config, 'quality', 100);
//        $mode = ArrayHelper::getValue($config, 'mode', ManipulatorInterface::THUMBNAIL_OUTBOUND);
//
//        if (!$width || !$height) {
//            $image = Image::getImagine()->open($path);
//            $ratio = $image->getSize()->getWidth() / $image->getSize()->getHeight();
//            if ($width) {
//                $height = ceil($width / $ratio);
//            } else {
//                $width = ceil($height * $ratio);
//            }
//        }
//
//        // Fix error "PHP GD Allowed memory size exhausted".
//        ini_set('memory_limit', '512M');
//        Image::thumbnail($path, $width, $height, $mode)->save($thumbPath, ['quality' => $quality]);
//    }
}