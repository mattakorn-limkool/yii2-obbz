<?php

namespace obbz\yii2\actions;

use Yii;
use yii\base\Action;
use yii\helpers\FileHelper;

/**
 * CkeditorUploadAction handles image uploads specifically for CKEditor.
 * It automatically converts uploaded images to WebP format and resizes them
 * if they exceed the specified maximum width.
 */
class CkeditorUploadAction extends Action
{
    /**
     * @var string Alias for the directory where files will be saved.
     */
    public $uploadPath = '@uploadPath/cke-img-upload';

    /**
     * @var string Base URL for accessing the uploaded images.
     */
    public $uploadUrl = '@uploadUrl/cke-img-upload';

    /**
     * @var int Maximum allowed width in pixels.
     */
    public $maxWidth = 1200;

    /**
     * @var int WebP output quality (0-100).
     */
    public $quality = 85;

    /**
     * @var array List of allowed file extensions.
     */
    public $allowExt = ['jpg', 'jpeg', 'png', 'webp'];

    /**
     * Runs the action.
     * @return mixed
     */
    public function run()
    {
        if (isset($_FILES['upload'])) {
            $targetPath = Yii::getAlias($this->uploadPath);
            $tempFile = $_FILES['upload']['tmp_name'];
            $fileInfo = pathinfo($_FILES['upload']['name']);

            // Change file extension to .webp for the target file
            $uploadFileName = time() . '-' . uniqid() . '.webp';
            $targetFile = $targetPath . DIRECTORY_SEPARATOR . $uploadFileName;

            // CKEditor Parameters
            $funcNum = Yii::$app->request->get('CKEditorFuncNum');
            $url = '';

            // 1. Validate file size (based on your app configuration)
            $fileSize = $_FILES["upload"]["size"];
            $maxSize = Yii::$app->params['upload.maxSize'] ?? (5 * 1024 * 1024); // Fallback to 5MB
            if ($fileSize > $maxSize) {
                return $this->sendResult($funcNum, $url, Yii::t('obbz', 'Image size is too large.'));
            }

            // 2. Validate file extension
            $allowExt = $this->allowExt;
            $extension = strtolower($fileInfo['extension']);
            if (!in_array($extension, $allowExt)) {
                return $this->sendResult($funcNum, $url, Yii::t('obbz', 'Allowed extensions: ') . implode(',', $allowExt));
            }

            // 3. Create target directory if it does not exist
            if (!file_exists($targetPath)) {
                FileHelper::createDirectory($targetPath, 0777);
            }

            try {
                // 4. Create Image Resource based on original format
                switch ($extension) {
                    case 'jpeg':
                    case 'jpg': $srcImg = imagecreatefromjpeg($tempFile); break;
                    case 'png': $srcImg = imagecreatefrompng($tempFile); break;
                    case 'webp': $srcImg = imagecreatefromwebp($tempFile); break;
                    default: throw new \Exception("Unsupported format");
                }

                $width = imagesx($srcImg);
                $height = imagesy($srcImg);

                // 5. Processing Resize Logic
                if ($width > $this->maxWidth) {
                    $newWidth = $this->maxWidth;
                    $newHeight = (int)floor($height * ($this->maxWidth / $width));
                    $destImg = imagecreatetruecolor($newWidth, $newHeight);

                    // Preserve Transparency for PNG and WebP
                    imagealphablending($destImg, false);
                    imagesavealpha($destImg, true);

                    imagecopyresampled($destImg, $srcImg, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                } else {
                    $destImg = $srcImg;
                }

                // 6. Save the image as WebP
                if (imagewebp($destImg, $targetFile, $this->quality)) {
                    // Free up memory
                    imagedestroy($srcImg);
                    if ($destImg !== $srcImg) {
                        imagedestroy($destImg);
                    }

                    $url = Yii::getAlias($this->uploadUrl) . '/' . $uploadFileName;
                    return $this->sendResult($funcNum, $url, '');
                } else {
                    throw new \Exception("Failed to save WebP image.");
                }

            } catch (\Exception $e) {
                return $this->sendResult($funcNum, $url, $e->getMessage());
            }
        }
        return false;
    }

    /**
     * Sends the result back to CKEditor via JavaScript.
     * @param int $funcNum The CKEditor function number to call back.
     * @param string $url The URL of the uploaded image.
     * @param string $message The error message, if any.
     * @return string
     */
    protected function sendResult($funcNum, $url, $message)
    {
        // This returns a JS block that CKEditor's upload window expects to receive.
        return "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction($funcNum, '$url', '$message');</script>";
    }
}