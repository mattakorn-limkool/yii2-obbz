<?php
namespace obbz\yii2\admin\controllers;
use yii\web\Controller;

/**
 * @author  Mattakorn Limkool <obbz.dev@gmail.com>
 *
 */
class DefaultController extends Controller
{
    public $title = 'Obbz Admin';
    public function actionIndex()
    {
        echo $this->title;
        return $this->render('index');
    }
}