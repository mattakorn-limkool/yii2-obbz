<?php
namespace obbz\yii2\widgets;

use \Yii;

class Alert extends \yii\bootstrap\Widget
{
    public $alertTypes = [
        'error'   => 'danger',
        'danger'  => 'danger',
        'success' => 'success',
        'info'    => 'info',
        'warning' => 'warning'
    ];

    public $pluginOptions = [];



    public function init(){
        parent::init();


        $session = Yii::$app->session;
        $flashes = $session->getAllFlashes();
        $textMessage = "";
        $hasFlashes = false;
        foreach ($flashes as $type => $data) {
            $hasFlashes = true;
            if (isset($this->alertTypes[$type])) {
                $data = (array) $data;
                foreach ($data as $i => $message) {
//                    $title = ucfirst($type);
                    echo \kartik\widgets\Growl::widget(array_merge([
                        'type' => $this->alertTypes[$type],
//                        'icon' => 'glyphicon glyphicon-ok-sign',
//                        'title' => $title,
                        'showSeparator' => true,
                        'body' => $message,
                        'pluginOptions'=>[
                            'showProgressbar'=>true,
                            'mouse_over' =>'pause',
//                            'delay'=>500000000
                        ]
                    ], $this->pluginOptions));
                }

                $session->removeFlash($type);
            }
        }


    }


}
