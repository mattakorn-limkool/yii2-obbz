<?php
namespace obbz\yii2\widgets\fileupload\validators;
use obbz\yii2\utils\ObbzYii;
use yii\base\Exception;
use yii\validators\Validator;

/**
 * @author: Mattakorn Limkool
 *
 */
class MultipleFilesValidator extends Validator
{
    public $min;
    public $max;

    public function validateAttribute($model, $attribute)
    {

        try{
            $countFile = 0;
            $folderPath = isset($model->id) ? $model->id : \Yii::$app->session->id;

            $directory = $model->getMultipleUploadPath($attribute, $folderPath);

            if(file_exists($directory)){
                $images = scandir($directory);
                foreach($images as $fileName){
                    if(!in_array($fileName,array(".",".."))){
                        $countFile++;
                    }
                }
            }
            if(isset($this->min) && $countFile < $this->min){
                $this->addError($model, $attribute, 'Must be upload at least {min} File', ['min' => $this->min]);
            }
            if(isset($this->max) && $countFile > $this->max){
                $this->addError($model, $attribute, 'Must be upload not more than {max} File', ['max' => $this->max]);
            }
        }catch (Exception $e){
            throw new Exception('Model must be attached MultipleUploadBehavior before use this validate');
        }

    }
}