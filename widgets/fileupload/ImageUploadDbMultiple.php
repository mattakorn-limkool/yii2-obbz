<?php
namespace obbz\yii2\widgets\fileupload;
use dosamigos\fileupload\FileUploadUI;

/**
 * @author: Mattakorn Limkool
 *
 */
class ImageUploadDbMultiple extends FileUploadMultiple
{

    public $formView = 'image/db/form';
    /**
     * @var string the upload view path to render the js upload template
     */
    public $uploadTemplateView = 'image/db/upload';
    /**
     * @var string the download view path to render the js download template
     */
    public $downloadTemplateView = 'image/db/download';
    /**
     * @var string the gallery
     */
    public $galleryTemplateView = 'image/db/gallery';

    public $optoins = ['accept' => 'image/*'];

    public $isHardReset = true;

    public function init(){
        parent::init();
        if($this->model->isNewRecord){
//            $modelItemClass = $this->model->getUploadItemModel();
            $this->model->resetItemsByUserSession($this->isHardReset);
        }

    }

}