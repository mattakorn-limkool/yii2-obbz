<?php
namespace obbz\yii2\widgets\fileupload;
use dosamigos\fileupload\FileUploadUI;
use obbz\yii2\utils\ArrayHelper;
use obbz\yii2\utils\ObbzYii;
use yii\helpers\Json;
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
        'maxFileSize' => null,
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
        $this->clientOptions['maxFileSize'] = isset($this->clientOptions['maxFileSize']) ? :\Yii::$app->params['upload.maxSize'];

    }

    public function registerClientScript()
    {
        $view = $this->getView();
        FileUploadAssets::register($view);

        if ($this->gallery) {
            GalleryAsset::register($view);
        }

        FileUploadUIAsset::register($view);

        $options = Json::encode($this->clientOptions);
        $id = $this->options['id'];

        $js[] = ";jQuery('#$id').fileupload($options);";
        if (!empty($this->clientEvents)) {
            foreach ($this->clientEvents as $event => $handler) {
                $js[] = "jQuery('#$id').on('$event', $handler);";
            }
        }
        $view->registerJs(implode("\n", $js));

        if ($this->load) {
            $view->registerJs("
                $('#$id').addClass('fileupload-processing');
                $.ajax({
                    url: $('#$id').fileupload('option', 'url'),
                    dataType: 'json',
                    context: $('#$id')[0]
                }).always(function () {
                    $(this).removeClass('fileupload-processing');
                }).done(function (result) {
                    $(this).fileupload('option', 'done').call(this, $.Event('done'), {result: result});
                });
            ");
        }


//        parent::registerClientScript();
    }



}