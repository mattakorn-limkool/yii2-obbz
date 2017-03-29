<?php
/**
 * Created by PhpStorm.
 * User: mattakorn
 * Date: 22/2/2560
 * Time: 3:04
 */

namespace obbz\yii2\controllers;

use obbz\yii2\models\CoreActiveRecord;
use obbz\yii2\utils\ObbzYii;

class CoreController extends  \yii\web\Controller
{
    /** @var  $modelClass CoreActiveRecord */
    public $modelClass;


    public function actions()
    {
        return [

            'publish' => [
                'class' => \obbz\yii2\actions\CorePublish::className(),
                'modelClass' => $this->modelClass,
                'redirectUrl' => $this->mainPageUrl()
            ],
            'publish-selected' => [
                'class' => \obbz\yii2\actions\CorePublishSelected::className(),
                'modelClass' => $this->modelClass,
                'redirectUrl' => $this->mainPageUrl()
            ],

            'unpublish' => [
                'class' => \obbz\yii2\actions\CoreUnpublish::className(),
                'modelClass' => $this->modelClass,
                'redirectUrl' => $this->mainPageUrl()
            ],
            'unpublish-selected' => [
                'class' => \obbz\yii2\actions\CoreUnpublishSelected::className(),
                'modelClass' => $this->modelClass,
                'redirectUrl' => $this->mainPageUrl()
            ],

            'active' => [
                'class' => \obbz\yii2\actions\CoreActive::className(),
                'modelClass' => $this->modelClass,
                'redirectUrl' => $this->mainPageUrl()
            ],
            'active-selected' => [
                'class' => \obbz\yii2\actions\CoreActiveSelected::className(),
                'modelClass' => $this->modelClass,
                'redirectUrl' => $this->mainPageUrl()
            ],

            'delete' => [
                'class' => \obbz\yii2\actions\CoreDelete::className(),
                'modelClass' => $this->modelClass,
                'redirectUrl' => $this->mainPageUrl()
            ],
            'delete-selected' => [
                'class' => \obbz\yii2\actions\CoreDeleteSelected::className(),
                'modelClass' => $this->modelClass,
                'redirectUrl' => $this->mainPageUrl()
            ],

            'sorting' => [
                'class' => \obbz\yii2\actions\CoreSorting::className(),
                'modelClass' => $this->modelClass,
                'redirectUrl' => $this->mainPageUrl()
            ],

            'translate' => [
                'class' => \obbz\yii2\actions\CoreTranslate::className(),
                'modelClass' => $this->modelClass,
//                'redirectUrl' => $this->mainPageUrl()
            ],
        ];
    }

    /**
     * for every action has success then redirect to here
     * @return array
     */
    public function mainPageUrl(){
        return ['index','key'=>ObbzYii::get('key')];
    }
}