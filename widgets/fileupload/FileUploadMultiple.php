<?php
namespace obbz\yii2\widgets\fileupload;
use dosamigos\fileupload\FileUploadUI;
use obbz\yii2\utils\ObbzYii;
use yii\helpers\Url;

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

    public function init(){
        parent::init();

        if(!isset($this->url['id'])){
            $this->url['id'] = $this->model->id;
        }
        if(!isset($this->url['field'])){
            $this->url['field'] = $this->attribute;
        }

        $this->clientOptions['url'] = Url::to($this->url);

    }

    public function registerClientScript()
    {
        $view = $this->getView();
        FileUploadAssets::register($view);
        parent::registerClientScript();
    }

}