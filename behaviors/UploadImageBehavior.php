<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\behaviors;


use yii\db\BaseActiveRecord;

class UploadImageBehavior extends  \mongosoft\file\UploadImageBehavior
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
        $path = strval(str_replace("\0", "", $path));
        $fileName = ($old === true) ? $model->getOldAttribute($attribute) : $model->$attribute;

        $fileName ? \Yii::getAlias($path . '/' . $fileName) : null;
    }
}