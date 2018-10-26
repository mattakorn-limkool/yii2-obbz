<?php
namespace obbz\yii2\widgets;

use \Yii;

class Alert extends \yii\bootstrap\Widget
{
    const MODE_GROWL = 'growl';
    const MODE_SWEETALERT = 'sweetalert';

    public $mode = self::MODE_GROWL; // 'growl' or 'sweetalert'

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

        if($this->mode == self::MODE_SWEETALERT){
            \yii2mod\alert\Alert::widget();
        }else {
            $session = Yii::$app->session;
            $flashes = $session->getAllFlashes();
            $textMessage = "";
            $hasFlashes = false;
            foreach ($flashes as $type => $data) {
                $hasFlashes = true;
                if (isset($this->alertTypes[$type])) {
                    $data = (array)$data;
                    foreach ($data as $i => $message) {
//                    $title = ucfirst($type);
                        \kartik\widgets\Growl::widget(array_merge([
                            'type' => $this->alertTypes[$type],
//                        'icon' => 'glyphicon glyphicon-ok-sign',
//                        'title' => $title,
                            'showSeparator' => true,
                            'body' => $message,
                            'pluginOptions' => [
                                'showProgressbar' => true,
                                'mouse_over' => 'pause',
//                            'delay'=>500000000
                            ]
                        ], $this->pluginOptions));
                    }

                    $session->removeFlash($type);
                }
            }
        }

    }


}
