<?php
namespace obbz\yii2\actions;
use obbz\yii2\utils\ArrayHelper;
use obbz\yii2\utils\ObbzYii;
use Yii;
use yii\base\Action;
use yii\web\Response;
use yii\helpers\Json;

/**
 * this action need set "enableCsrfValidation" to false at beforeAction
 *
 */
class FacebookDataDeletionAction extends Action
{
    public $appSecret;

    /** @var callable ฟังก์ชันสำหรับลบข้อมูลใน Database */
//    public $deletionCallback;

    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!isset($this->appSecret)) {
            $this->appSecret = ArrayHelper::getValue(ObbzYii::app()->params, "social.facebook.clientSecret");
            if (!isset($this->appSecret)){
                return ['error' => 'Invalid meta secret'];
            }
        }

        $signedRequest = Yii::$app->request->post('signed_request');

        if (!$signedRequest) {
            return ['error' => 'No signed request provided'];
        }

        $data = $this->parseSignedRequest($signedRequest);

        if ($data && isset($data['user_id'])) {
            $userId = $data['user_id'];

            // เรียกใช้ Callback ที่ส่งมาจาก Controller เพื่อลบข้อมูล
//            if (is_callable($this->deletionCallback)) {
//                call_user_func($this->deletionCallback, $userId);
//            }
            // todo - set not active to user by facbook_user_id ($data['user_id'])

            $confirmationCode = 'DEL_' . $userId . '_' . time();

            return [
                'url' => \yii\helpers\Url::to(['deletion-status', 'id' => $confirmationCode], true),
                'confirmation_code' => $confirmationCode,
            ];
        }

        return ['error' => 'Invalid signed request'];
    }

    protected function parseSignedRequest($signed_request)
    {
        list($encoded_sig, $payload) = explode('.', $signed_request, 2);

        $sig = base64_decode(strtr($encoded_sig, '-_', '+/'));
        $data = Json::decode(base64_decode(strtr($payload, '-_', '+/')));

        $expected_sig = hash_hmac('sha256', $payload, $this->appSecret, true);

        if ($sig !== $expected_sig) {
            Yii::error('Facebook Data Deletion: Signature mismatch');
            return null;
        }

        return $data;
    }
}