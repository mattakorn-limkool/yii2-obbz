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
    public $showTitle = true;
    public $headerActions = [];

    public function actions()
    {
        return [

            'publish' => [
                'class' => \obbz\yii2\actions\CorePublish::class,
                'modelClass' => $this->getModelClass(),
                'redirectUrl' => $this->mainPageUrl()
            ],
            'publish-selected' => [
                'class' => \obbz\yii2\actions\CorePublishSelected::class,
                'modelClass' => $this->getModelClass(),
                'redirectUrl' => $this->mainPageUrl()
            ],

            'unpublish' => [
                'class' => \obbz\yii2\actions\CoreUnpublish::class,
                'modelClass' => $this->getModelClass(),
                'redirectUrl' => $this->mainPageUrl()
            ],
            'unpublish-selected' => [
                'class' => \obbz\yii2\actions\CoreUnpublishSelected::class,
                'modelClass' => $this->getModelClass(),
                'redirectUrl' => $this->mainPageUrl()
            ],

            'active' => [
                'class' => \obbz\yii2\actions\CoreActive::class,
                'modelClass' => $this->getModelClass(),
                'redirectUrl' => $this->mainPageUrl()
            ],
            'active-selected' => [
                'class' => \obbz\yii2\actions\CoreActiveSelected::class,
                'modelClass' => $this->getModelClass(),
                'redirectUrl' => $this->mainPageUrl()
            ],

            'delete' => [
                'class' => \obbz\yii2\actions\CoreDelete::class,
                'modelClass' => $this->getModelClass(),
                'redirectUrl' => $this->mainPageUrl()
            ],
            'delete-selected' => [
                'class' => \obbz\yii2\actions\CoreDeleteSelected::class,
                'modelClass' => $this->getModelClass(),
                'redirectUrl' => $this->mainPageUrl()
            ],

            'sorting' => [
                'class' => \obbz\yii2\actions\CoreSorting::class,
                'modelClass' => $this->getModelClass(),
                'redirectUrl' => $this->mainPageUrl()
            ],

            'translate' => [
                'class' => \obbz\yii2\actions\CoreTranslate::class,
                'modelClass' => $this->getModelClass(),
//                'redirectUrl' => $this->mainPageUrl()
            ],
        ];
    }

    /**
     * for every action has success then redirect to here
     * @return array
     */
    public function mainPageUrl(){
//        return ['index','key'=>ObbzYii::get('key')];
        return ObbzYii::getReturnUrl(['index','key'=>ObbzYii::get('key')]);
    }

    public function getModelClass(){
        return $this->modelClass;
    }
}