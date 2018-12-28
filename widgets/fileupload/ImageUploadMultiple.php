<?php
namespace obbz\yii2\widgets\fileupload;
use dosamigos\fileupload\FileUploadUI;

/**
 * @author: Mattakorn Limkool
 *
 */
class ImageUploadMultiple extends FileUploadMultiple
{
    public $formView = 'image/form';
    /**
     * @var string the upload view path to render the js upload template
     */
    public $uploadTemplateView = 'image/upload';
    /**
     * @var string the download view path to render the js download template
     */
    public $downloadTemplateView = 'image/download';
    /**
     * @var string the gallery
     */
    public $galleryTemplateView = 'image/gallery';

    public $optoins = ['accept' => 'image/*'];

}