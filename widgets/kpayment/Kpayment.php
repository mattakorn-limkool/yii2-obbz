<?php
/**
 * @author  Mattakorn Limkool <obbz.dev@gmail.com>
 *
 */

namespace obbz\yii2\widgets\kpayment;


use obbz\yii2\utils\ArrayHelper;
use obbz\yii2\utils\Curl;
use obbz\yii2\utils\ObbzYii;
use yii\base\Component;
use yii\base\Widget;
use yii\helpers\Html;
use yii\httpclient\Client;


class Kpayment extends Component
{
    const MODE_CARD = 'card';
    const MODE_WECHAT = 'wechat';
    const MODE_ALIPAY = 'alipay';
    const MODE_QR = 'qr';

    const TS_STATUS_INITIALIZE = 'Initialize'; //  Payment transaction initialized
    const TS_STATUS_PRE_AUTHORIZED = 'Pre-Authorized'; // Payment need to do authentication 3D secure
    const TS_STATUS_AUTHORIZED = 'Authorized'; // Authorized success
    const TS_STATUS_DECLINED = 'Declined'; // Reject payment from host
    const TS_STATUS_REVERSED = 'Reversed'; // Payment failed from system reject
    const TS_STATUS_VOIDED = 'Voided'; // Payment failed from system reject
    const TS_STATUS_REFUND_SENT = 'Refund Sent'; // Payment failed from system reject

    public $mode = self::MODE_CARD;

    ##region mandatory
    public $isSandbox = true;
    public $pkey;
    public $skey;
    ##endregion

    ##region credit card
    public $cardMid;
    public $cardTid;
    ##endregion

    ##region e-wallet
    public $ewalletMid;
    // wechat
    public $wechatTid;
    // alipay

    public $alipayTid;
    ##endregion


    public $sandboxEndpoint = [
        'ui'=>'https://dev-kpaymentgateway.kasikornbank.com',
        'api'=>'https://dev-kpaymentgateway-services.kasikornbank.com',
    ];
    public $prodEnpoint = [
        'ui'=>'https://kpaymentgateway.kasikornbank.com',
        'api'=>'https://kpaymentgateway-services.kasikornbank.com',
    ];

    public $uiRoutes = [
        'base' => '/ui/v2/kpayment.min.js',
    ];
    public $apiRoutes = [
        'card/charge' => '/card/v2/charge',
        'card/inquiry' => '/card/v2/charge/{refId}',
        'card/void' => '/card/v2/charge/{chargeId}/Void',
        'card/refund' => '/card/v2/charge/{chargeId}/refund',

        'qr/order' => '/qr/v2/order',
        'qr/inquiry-order' => '/qr/v2/order/{orderId}',
        'qr/inquiry-transaction' => '/qr/v2/qr/{refId}',
    ];


    /**
     * @inheritdoc
     */
    public function init()
    {

        ArrayHelper::requiredModelValue($this, 'pkey');
        ArrayHelper::requiredModelValue($this, 'skey');

        parent::init();
    }

##region  card
    /**
     * @param $data array request data to create charge
     * @return \yii\httpclient\Response
     */
    public function callCreateCharge($data){
        $route = $this->getRoute($this->apiRoutes['card/charge']);
        $res = $this->callPostApi($route, $data);
        return $res;
    }


    /**
     * @param $refId {charge_id | reference_order | order_id}
     * @param $transactionDate
     * @return \yii\httpclient\Response
     */
    public function callCardUpdateStatus($refId, $transactionDate = null){
        $route = $this->getRoute($this->apiRoutes['card/inquiry'], compact('refId'));
        if($transactionDate)
            $route .= '/'.$transactionDate;
        $res = $this->callGetApi($route);
        return $res;
    }

    /**
     * @param $chargeId
     * @param $data
     * @return \yii\httpclient\Response
     */
    public function callCardVoid($chargeId, $data = []){
        $route = $this->getRoute($this->apiRoutes['card/void'], compact('chargeId'));
        $res = $this->callPostApi($route, $data);
        return $res;
    }

    /**
     * @param $chargeId
     * @param array $data required ['reason'=>'', 'amount'=>'']
     * @return \yii\httpclient\Response
     */
    public function callCardRefund($chargeId, $data = []){
        $route = $this->getRoute($this->apiRoutes['card/refund'], compact('chargeId'));
        $res = $this->callPostApi($route, $data);
        return $res;
    }
##endregion

##region qr
    /**
     * for qr and weChat
     * @param $data array
     * @return \yii\httpclient\Response
     */
    public function callCreateOrder($data){
        $res = $this->callPostApi($this->apiRoutes['qr/order'], $data);
        return $res;
    }

    /**
     * @param $refId {charge_id | order_id}
     * @param null $transactionDate
     * @return \yii\httpclient\Response
     */
    public function callQrUpdateStatus($refId){
        $route = $this->getRoute($this->apiRoutes['qr/inquiry-transaction'], compact('refId'));
        $res = $this->callGetApi($route);
        return $res;
    }

#endregion

    /**
     * call by order has autorized by kbank support (card,wechat, alipay, qr)
     * @param string $refId  prefer order_id can find every case
     * @param string $mode
     * @param $res
     * @param null $transactionDate
     * @return bool
     */
    public function callAutorizedStatus($refId, $mode, &$res, $transactionDate=null){
        $res = $this->inquiryTansaction($refId, $mode, $transactionDate);
        if($res && $res->isOk && $res->content){
            $data = json_decode($res->content);
            $valid = $this->canAutorizedStatus($data, false);
            return $valid;
        }
        return false;
    }


    /**
     * util for check can autorized
     * @param $bankData
     * @param bool|true $doChecksum
     * @return bool
     * @throws \Exception
     */
    public function canAutorizedStatus($bankData, $doChecksum = true){
        $state = isset($bankData->transaction_state) ? $bankData->transaction_state: '';
        $status = isset($bankData->status) ? $bankData->status: '';
        $checkSumresult = $doChecksum ? $this->checksum($bankData) : true;

        return $state == self::TS_STATUS_AUTHORIZED && $status == 'success' && $checkSumresult;
    }

    public function canVoid($bankData){
        $state = isset($bankData->transaction_state) ? $bankData->transaction_state: '';
        $status = isset($bankData->status) ? $bankData->status: '';
        return $state == self::TS_STATUS_VOIDED && $status == 'success';
    }
    public function canRefund($bankData){
        $state = isset($bankData->transaction_state) ? $bankData->transaction_state: '';
        $status = isset($bankData->status) ? $bankData->status: '';
        return $state == self::TS_STATUS_REFUND_SENT && $status == 'success';
    }



    /**
     * easy way to call update-status by mode
     * @param $refId
     * @param $mode
     * @param null $transactionDate
     * @return bool|\yii\httpclient\Response
     */
    public function inquiryTansaction($refId, $mode, $transactionDate = null){
        if(in_array($mode, [self::MODE_CARD, self::MODE_ALIPAY] )){
            return $this->callCardUpdateStatus($refId, $transactionDate);
        }else if($mode == self::MODE_WECHAT){
            return $this->callQrUpdateStatus($refId);
        }
        return false;
    }



    public function checksum($webHookNotifyData){
        if(isset($webHookNotifyData))
        $checksum = ArrayHelper::getValue($webHookNotifyData, 'checksum');
        $calChecksum =
            ArrayHelper::getValue($webHookNotifyData, 'id') .
            number_format((float)ArrayHelper::getValue($webHookNotifyData, 'amount'), 4, '.', '') .
            ArrayHelper::getValue($webHookNotifyData, 'currency') .
            ArrayHelper::getValue($webHookNotifyData, 'status') .
            ArrayHelper::getValue($webHookNotifyData, 'transaction_state') .
            $this->skey
        ;
        return hash('sha256',$calChecksum) === $checksum;
    }

    public static function formatAmount($amount){
        return number_format((float)$amount, 2, '.', '');
    }

    protected function getUiUrl($route = ''){
        return $this->isSandbox ? $this->sandboxEndpoint['ui'] . $route: $this->prodEnpoint['ui'] . $route;
    }
    protected function getApiUrl($route = ''){
        return $this->isSandbox ? $this->sandboxEndpoint['api'] . $route: $this->prodEnpoint['api'] . $route;
    }

    protected function callGetApi($route, $data = []){
        $url = $this->getApiUrl($route);
        $request = new Client();

        $res = $request->createRequest()
            ->setFormat(Client::FORMAT_JSON)
            ->setUrl($url)
            ->setMethod('GET')
            ->setHeaders([
                'content-type' => 'application/json',
                'x-api-key'=>$this->skey,
            ])
            ->send();

        return $res;
    }

    protected function callPostApi($route, $data = []){
        $url = $this->getApiUrl($route);
        $data = json_encode($data);
        $request = new Client();

        $res = $request->createRequest()
            ->setFormat(Client::FORMAT_JSON)
            ->setUrl($url)
            ->setMethod('POST')
            ->setHeaders([
                'content-type' => 'application/json',
                'x-api-key'=>$this->skey,
            ])
            ->setContent($data)
            ->send();


//        ObbzYii::debug( $res->content);
        return $res;
    }

    protected function getRoute($url, $params = []){
        $search = [];
        $replace = [];
        foreach($params as $key=>$val){
            $search[] = "{". $key. "}";
            $replace[] = $val;
        }

        return str_replace($search,  $replace, $url);
    }


    #region UI
    public function uiPayNowBtn($scriptAttrbutes = []){
        $attrs = [
            'src' => $this->getUiUrl($this->uiRoutes['base']),
            'data-apikey' => $this->pkey,
            'data-name' => ObbzYii::app()->name,
        ];

        $attrs = array_merge($attrs, $scriptAttrbutes);
        return Html::script('', $attrs);
    }
    #endregin
}