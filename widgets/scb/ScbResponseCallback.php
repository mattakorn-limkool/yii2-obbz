<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\widgets\scb;

use yii\base\Model;

/**
 * Class ScbResponseCallback
 * @package frontend\components
 *
 */


class ScbResponseCallback extends Model
{
    public $mid;
    public $terminal;
    public $command;
    public $ref_no;
    public $trans_no;
    public $trans_date;
    public $amount;
    public $cur_abbr;
    public $payment_status;
    public $payment_type;
    public $appr_code;
    public $eci;
    public $response_code;
    public $response_message;
    public $card_no;
    public $usrdat1;
    public $usrdat2;
    public $usrdat3;
    public $usrdat4;
    public $usrdat5;
    public $usrdat6;
    public $usrdat7;
    public $usrdat8;
    public $usrdat9;
    public $usrdat10;

    public function rules()
    {
        return array_merge(parent::rules(),[
            [$this->fields(), 'safe'],
        ]);
    }
}