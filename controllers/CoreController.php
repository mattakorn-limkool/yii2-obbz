<?php
/**
 * Created by PhpStorm.
 * User: mattakorn
 * Date: 22/2/2560
 * Time: 3:04
 */

namespace obbz\yii2\controllers;

use obbz\yii2\models\CoreActiveRecord;

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
            ],
            'publish-selected' => [
                'class' => \obbz\yii2\actions\CorePublishSelected::className(),
                'modelClass' => $this->modelClass,
            ],

            'unpublish' => [
                'class' => \obbz\yii2\actions\CoreUnpublish::className(),
                'modelClass' => $this->modelClass,
            ],
            'unpublish-selected' => [
                'class' => \obbz\yii2\actions\CoreUnpublishSelected::className(),
                'modelClass' => $this->modelClass,
            ],

            'active' => [
                'class' => \obbz\yii2\actions\CoreActive::className(),
                'modelClass' => $this->modelClass,
            ],
            'active-selected' => [
                'class' => \obbz\yii2\actions\CoreActiveSelected::className(),
                'modelClass' => $this->modelClass,
            ],

            'delete' => [
                'class' => \obbz\yii2\actions\CoreDelete::className(),
                'modelClass' => $this->modelClass,
            ],
            'delete-selected' => [
                'class' => \obbz\yii2\actions\CoreDeleteSelected::className(),
                'modelClass' => $this->modelClass,
            ],

            'sorting' => [
                'class' => \obbz\yii2\actions\CoreSorting::className(),
                'modelClass' => $this->modelClass,
            ],
        ];
    }

}