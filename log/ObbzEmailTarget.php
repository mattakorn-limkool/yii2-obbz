<?php

namespace obbz\yii2\log;

use yii\log\EmailTarget;

class ObbzEmailTarget extends EmailTarget
{
    public $levels = ['error'];
    public $message = [
        'from' => ['aob.test.mail@gmail.com'],
        'to' => ['obbz.dev@gmail.com'],
        'subject' => 'Application Error ',
    ];

    public function export()
    {
        $request = \Yii::$app->request;
        $host = $request->getHostInfo();
        $this->message['subject'] = 'Website Error (' . $host . ')';

        parent::export();
    }

}