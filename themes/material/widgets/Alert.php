<?php
namespace obbz\yii2\themes\material\widgets;

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

    public function init()
    {
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
                    $textMessage .= 'Obbz.alert.message("' . $this->alertTypes[$type] . '","' .$message . '");';
                }

                $session->removeFlash($type);
            }
        }
        if($hasFlashes)
            echo    '<script>
                    $(document).ready(function(){
                            ' . $textMessage .
                    '});
                    </script>';
    }

//    public function run(){}
}
