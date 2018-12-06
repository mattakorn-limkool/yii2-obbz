<?php
namespace obbz\yii2\widgets\scb;
use yii\base\Component;

/**
 * @author: Mattakorn Limkool
 *
 */
class ScbPayment extends Component
{
    const PROD_URL =            'https://nsips.scb.co.th/NSIPSWeb/NsipsMessageAction.do';
    const PROD_INQUIRY_URL =    'https://nsips.scb.co.th/NSIPSWeb/NsipsMessageAction.do';

    const TEST_URL =            'https://nsips-test.scb.co.th:443/NSIPSWeb/NsipsMessageAction.do';
    const TEST_INQUIRY_URL =    'https://nsips-test.scb.co.th:443/NSIPSWeb/NsipsMessageAction.do';

    const PAYMENT_STATUS_APPROVE = '002';
    const PAYMENT_STATUS_REJECT = '003';
    const PAYMENT_STATUS_ERROR = '006';

    const PAYMENT_TYPE_CREDITCARD = 'C';
    const PAYMENT_TYPE_VISA = '01';
    const PAYMENT_TYPE_MASTERCARD = '02';
    const PAYMENT_TYPE_JCB = '03';
    const PAYMENT_TYPE_SCB_VISA = '11';
    const PAYMENT_TYPE_SCB_MAASTERCARD = '12';
    const PAYMENT_TYPE_SCB_JCB = '13';


    public $isProduction = false;

    /**
     * can set via global param scb.merchantId
     * @var $merchantId - Merchant ID is required
     */
    public $merchantId = null;
    /**
     * can set via glabal param scb.terminalId
     * @var $terminalId - Terminal ID is required
     */
    public $terminalId = null;



    public function init(){

        if(isset(\Yii::$app->params['scb.merchantId'])){
            $this->merchantId = \Yii::$app->params['scb.merchantId'];
        }
        if(isset(\Yii::$app->params['scb.terminalId'])){
            $this->terminalId = \Yii::$app->params['scb.terminalId'];
        }

        if(!isset($this->merchantId)){
            throw new \Exception('Please define merchantId');
        }
        if(!isset($this->terminalId)){
            throw new \Exception('Please define terminalId');
        }

        parent::init();
    }

    protected function defaultAuthorizationRequest($authorizationRequest){
        $defaultData = [
            'mid' => $this->merchantId,
            'terminal' => $this->terminalId,
            'version'=>null, // * Ignored in NSIPS
            'command'=>'CRAUTH', // * Fix “CRAUTH”
            'ref_no'=>null, // * Merchant reference number
            'ref_date'=>null, // * Merchant reference datetime 'yyyymmddhhiiss'
            'service_id'=>null, // * Start from 10-99
            'cust_id'=>null, //  Merchant customer id
            'cur_abbr'=>'THB', // * Currency   ;   case insensitive
            'amount'=>null, // * Amount     ;   only [0-9] and “.”  allow
            'cust_lname'=>'', // Card holder last name in English
            'cust_mname'=>'', // Card holder middle name in English
            'cust_fname'=>'', // Card holder first name in English
            'cust_email'=>'', //Card holder email address in English
            'cust_country'=>'TH', // Customer country code
            'cust_address1'=>'', // Customer address1
            'cust_address2'=>'', // Customer address2
            'cust_city'=>'', // Customer city
            'cust_province'=>'', // Customer province
            'cust_zip'=>'', // Customer zip
            'cust_phone'=>'', // Customer phone
            'cust_fax'=>'', // Customer fax
            'backURL'=>null, // Return URL
            'contact_flag'=> 1, // Return URL,
            'usrdat1'=>null,
            'usrdat2'=>null,
            'usrdat3'=>null,
            'usrdat4'=>null,
            'usrdat5'=>null,
            'usrdat6'=>null,
            'usrdat7'=>null,
            'usrdat8'=>null,
            'usrdat9'=>null,
            'usrdat10'=>null,
        ];

//        if(!$this->isProduction){
//
//        }

        return array_merge($defaultData, $authorizationRequest);
    }

    public function getAuthorizationRequestUrl($authorizationRequest){
        $url = $this->isProduction ? self::PROD_URL : self::TEST_URL;
        $params = $this->defaultAuthorizationRequest($authorizationRequest);
        return $url . '?' .http_build_query($params);
    }

    public static function getDatetimeFormat($time = null){
        return date('Ymdhis',$time);
    }

    public function checkMerchantData($merchantId, $terminalId){
        if($this->merchantId ==$merchantId && $this->terminalId ==$terminalId){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @return ScbResponseCallback
     */
    public function loadCallback(){
        $callback = new ScbResponseCallback();
        $callback->attributes = $_POST;
        return $callback;
    }
}