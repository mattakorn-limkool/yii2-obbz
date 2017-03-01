<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\behaviors;


class UploadBehavior extends \mongosoft\file\UploadBehavior
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
}