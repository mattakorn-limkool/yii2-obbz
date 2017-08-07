<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\utils;


class ThailandPost
{
    const SHIPPING_TYPE_NORMAL = 1;
    const SHIPPING_TYPE_REGISTER = 2;
    const SHIPPING_TYPE_EMS = 3;


    /**
     * @param $weight int - Unit is Gram only
     * @param $shippingType int
     */
    public static function shippingRate($weight, $shippingType, &$trackError = null){
        if($weight < 0){
            $trackError =  "ERROR!! - Wrong weight";
            return false;
        }

        $rate = 0;
        if($shippingType == self::SHIPPING_TYPE_NORMAL){
            $rate = 20;
            $iWeight = $weight -1000;
            while($iWeight  > 0){
                $rate  += 15;
                $iWeight -= 1000;
            }

        }else if($shippingType == self::SHIPPING_TYPE_REGISTER){
            if($weight <= 100) $rate = 18;
            else if($weight <= 250) $rate = 22;
            else if($weight <= 500) $rate = 28;
            else if($weight <= 1000) $rate = 38;
            else if($weight <= 2000) $rate = 58;
            else{
                $trackError = "ERROR!! - CAN NOT SEND MORE THAN 2000 Gram by shipping type Register";
                return false;
            };
        }else if($shippingType == self::SHIPPING_TYPE_EMS){
            if($weight <= 20) $rate = 32;
            else if($weight <= 100) $rate = 37;
            else if($weight <= 250) $rate = 42;
            else if($weight <= 500) $rate = 52;
            else if($weight <= 1000) $rate = 67;
            else if($weight <= 1500) $rate = 82;
            else if($weight <= 2000) $rate = 97;
            else if($weight <= 2500) $rate = 122;
            else if($weight <= 3000) $rate = 137;
            else if($weight <= 3500) $rate = 157;
            else if($weight <= 4000) $rate = 177;
            else if($weight <= 4500) $rate = 197;
            else if($weight <= 5000) $rate = 217;
            else if($weight <= 5500) $rate = 242;
            else if($weight <= 6000) $rate = 267;
            else if($weight <= 6500) $rate = 292;
            else if($weight <= 7000) $rate = 317;
            else if($weight <= 7500) $rate = 342;
            else if($weight <= 8000) $rate = 367;
            else if($weight <= 8500) $rate = 397;
            else if($weight <= 9000) $rate = 427;
            else if($weight <= 9500) $rate = 457;
            else if($weight <= 10000) $rate = 487;
            else{
                $trackError = "ERROR!! - Not implemented yet.";
                return false;
            };
        }else{
            $trackError = "ERROR!! - Wrong shipping type";
            return 0;
        }

        return $rate;
    }
}