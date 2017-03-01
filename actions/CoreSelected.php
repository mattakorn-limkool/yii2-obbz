<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\actions;

use obbz\yii2\utils\ObbzYii;

class CoreSelected extends CoreBaseAction
{
    public $errorPleaseSelectText = "Please select at least 1 item";
    public $data;

    public function validate(){
        $this->data = ObbzYii::post('selection');

        if(empty( $this->data )){
            ObbzYii::setFlashError(ObbzYii::t($this->errorPleaseSelectText));
            return false;
        }

        return true;
    }
}