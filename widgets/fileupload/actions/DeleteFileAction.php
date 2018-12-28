<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\widgets\fileupload\actions;

use obbz\yii2\behaviors\UploadImageBehavior;
use obbz\yii2\models\CoreBaseActiveRecord;
use obbz\yii2\utils\ArrayHelper;
use obbz\yii2\utils\ObbzYii;
use yii\base\Action;
use yii\base\InvalidConfigException;
use yii\helpers\FileHelper;
use yii\helpers\Json;
use yii\imagine\Image;
use yii\web\UploadedFile;

class DeleteFileAction extends Action
{
    public $modelClass;
    public $field;

    public function init(){
        if($this->modelClass == null){
            throw new InvalidConfigException('Please define $modelClass');
        }
        if($this->field == null){
            throw new InvalidConfigException('Please define $field');
        }
        parent::init();
    }

    /**
     * @param $name
     * @param null $id
     * @return string
     */
    public function run($name, $id = null){
        $folderPath = !empty($id) ? $id : Yii::$app->session->id;
        /** @var CoreBaseActiveRecord $model */
        $model = new $this->modelClass;

        $directory = $model->getMultipleUploadPath($this->field, $id);
        if (is_file($directory . DIRECTORY_SEPARATOR . $name)) {
            unlink($directory . DIRECTORY_SEPARATOR . $name);
        }

        $files = FileHelper::findFiles($directory);
        $output = [];
        foreach ($files as $file) {
            $fileName = basename($file);
            $path = \Yii::getAlias('@uploadUrl'). DIRECTORY_SEPARATOR .
                $model->tableName() . $folderPath . DIRECTORY_SEPARATOR . $fileName;
            $output['files'][] = [
                'name' => $fileName,
                'size' => filesize($file),
                'url' => $path,
                'thumbnailUrl' => $path,
                'deleteUrl' => 'image-delete?name=' . $fileName . '&id=' . $id,
                'deleteType' => 'POST',
            ];
        }
        return Json::encode($output);

    }

}