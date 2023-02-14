<?php
namespace obbz\yii2\components\line;
use yii\base\Component;
use yii\base\Exception;

/**
 * Class LineNotify
 * @package obbz\yii2\components\line
 * @author Mattakorn Limkool
 */
class LineNotify extends Component
{
    /*
     * default api for notification
     */
    const LINE_API = 'https://notify-api.line.me/api/notify';
    /**
     * set sendUserToken when send same user multiple times and varies message
     * @var $sendUserToken null
     */
    public $sendUserToken = null;

    /**
     * send message with setup token
     * @param $message
     * @return mixed
     * @throws Exception
     */
    public function send($message){
        if(isset($this->sendUserToken)){
            throw new Exception('Plese set sendUserToken before');
        }else{
            return self::sendByToken($message, $this->sendUserToken);
        }
    }

    /**
     * send
     * @param $message
     * @param $sendUserToken
     * @return mixed
     */
    public static function sendByToken($message, $sendUserToken){
        $queryData = array('message' => $message);
        $queryData = http_build_query($queryData,'','&');
        $headerOptions = array(
            'http' => array(
                'method' => 'POST',
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n"
                    ."Authorization: Bearer ". $sendUserToken ."\r\n"
                    ."Content-Length: ".strlen($queryData)."\r\n",
                'content' => $queryData
            )
        );
        $context = stream_context_create($headerOptions);
        $result = @file_get_contents(self::LINE_API, false, $context);
        if($result == false){
            return false;
        }

        $res = json_decode($result);
        return $res;
    }
}