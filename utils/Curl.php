<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\utils;


class Curl extends \linslin\yii2\curl\Curl
{
    public function asyncResponse(){
        $this->setOption(CURLOPT_TIMEOUT, 1);
        $this->setOption(CURLOPT_HEADER, 0);
        $this->setOption(CURLOPT_RETURNTRANSFER, false);
        $this->setOption(CURLOPT_FORBID_REUSE, true);
        $this->setOption(CURLOPT_CONNECTTIMEOUT, 1);
        $this->setOption(CURLOPT_DNS_CACHE_TIMEOUT, 10);
        $this->setOption(CURLOPT_FRESH_CONNECT, true);
        return $this;
    }
}