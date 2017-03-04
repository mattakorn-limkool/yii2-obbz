<?php
/**
 * @author Mattakorn Limkool
 */

namespace obbz\yii2\i18n;
use yii\helpers\Html;
use yii\i18n\Formatter;

class CoreFormatter extends Formatter
{
//    public $locale = 'th-TH';
    public $dateFormat = 'dd/MM/yyyy';
    public $datetimeFormat = 'dd/MM/yyyy hh:mm:ss';
    public $timeFormat = 'hh:mm:ss';
    public $decimalSeparator = '.';
    public $thousandSeparator = ',';
    public $currencyCode = 'THB';
    public $nullDisplay = '';

    /**
     * @param $value
     * @param int $length
     * @param string $tail
     * @return string
     */
    public function asOnlyText($value, $length = 200, $tail = "..." ){
        if ($value === null) {
            return $this->nullDisplay;
        }
        $value = strip_tags($value);
        $result = $this->truncateText($value, $length, $tail);
        return $result;
    }

    protected function truncateText($value, $length = 200, $tail = "..."){
        if (preg_match('/\p{Thai}/u', $value) === 1) { // for Thai
            $result  = mb_substr($value, 0, $length, 'UTF-8');
            if(strlen($value) > $length)
                $result .= $tail;
        }else{
            $parts = preg_split('/([\s\n\r]+)/', $value, null, PREG_SPLIT_DELIM_CAPTURE);
            $partsCount = count($parts);

            $textLength = 0;
            $lastPart = 0;
            $isMoreThanLength = false;
            for (; $lastPart < $partsCount; ++$lastPart) {
                $textLength += strlen($parts[$lastPart]);
                if ($textLength > $length) {$isMoreThanLength= true; break; }
            }
            $result = trim(implode(array_slice($parts, 0, $lastPart)));
            if($isMoreThanLength)
                $result .=  $tail;
        }

        return $result;
    }

}