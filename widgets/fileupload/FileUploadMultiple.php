<?php
namespace obbz\yii2\widgets\fileupload;
use dosamigos\fileupload\FileUploadUI;

/**
 * @author: Mattakorn Limkool
 *
 */
class FileUploadMultiple extends FileUploadUI
{
    public $formView = 'file/form';
    /**
     * @var string the upload view path to render the js upload template
     */
    public $uploadTemplateView = 'file/upload';
    /**
     * @var string the download view path to render the js download template
     */
    public $downloadTemplateView = 'file/download';
    /**
     * @var string the gallery
     */
    public $galleryTemplateView = 'file/gallery';

    public $clientOptions = [
        'maxFileSize' => 2000000,
        'autoUpload'=>true,
    ];

    public function registerClientScript()
    {
        $view = $this->getView();
        FileUploadAssets::register($view);
        parent::registerClientScript();
    }

}