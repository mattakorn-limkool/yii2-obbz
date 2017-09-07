<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\behaviors;


use obbz\yii2\utils\UploadedFile;

class UploadBehavior extends \mongosoft\file\UploadBehavior
{
//    public $isBase64Upload = false;


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

//    /**
//     * @param UploadedFile $file
//     * @param string $path
//     * @return bool
//     */
//    protected function save($file, $path)
//    {
//        return $file->saveAs($path, $this->deleteTempFile, $isBase64Upload);
//    }
}