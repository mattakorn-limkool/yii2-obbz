<?php
/**
 * @author Mattakorn Limkool
 */

namespace obbz\yii2\i18n;
use yii\helpers\FormatConverter;
use yii\i18n\Formatter;

class CoreFormatter extends Formatter
{
    const DB_DATE_FORMAT = 'php:Y-m-d';
    const DB_DATETIME_FORMAT = 'php:Y-m-d H:i:s';
    const DB_TIME_FORMAT = 'php:H:i:s';

//    public $locale = 'th-TH';
    public $dateFormat = 'dd/MM/yyyy';
    public $datetimeFormat = 'dd/MM/yyyy HH:mm:ss';
    public $timeFormat = 'HH:mm:ss';
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

    /**
     * convert date from default format to db format
     * @param $value
     * @param null $fromFormat
     * @return string
     */
    public function asDbDate($value = null, $fromFormat = null){
        if(!isset($fromFormat))
            $fromFormat = $this->dateFormat;
        $time =  $this->timeFromFormat($value, 'date', $fromFormat);
        return $this->asDate($time, self::DB_DATE_FORMAT);
    }

    /**
     * convert datetime from default format to db format
     * @param $value
     * @param null $fromFormat
     * @return string
     */
    public function asDbDatetime($value = null, $fromFormat = null){
        if(!isset($fromFormat))
            $fromFormat = $this->datetimeFormat;
        $time =  $this->timeFromFormat($value, 'datetime', $fromFormat);
        return $this->asDatetime($time, self::DB_DATETIME_FORMAT);
    }

    /**
     * convert time from default format to db format
     * @param $value
     * @param null $fromFormat
     * @return string
     */
    public function asDbTime($value = null, $fromFormat = null){
        if(!isset($fromFormat))
            $fromFormat = $this->timeFormat;
        $time =  $this->timeFromFormat($value, 'time', $fromFormat);
        return $this->asTime($time, self::DB_TIME_FORMAT);
    }

    /**
     * @param $datetime
     * @param $type
     * @param $format
     * @return \DateTime
     */
    public function timeFromFormat($datetime, $type, $format){
        if (strncmp($format, 'php:', 4) === 0) {
            $format = substr($format, 4);
        } else {
            $format = FormatConverter::convertDateIcuToPhp($format, $type, $this->locale);
        }

        if($datetime == null){ // get current datetime
            $datetime = date($format);
        }

//        echo $format . ' ' . $datetime;
        return \DateTime::createFromFormat($format, $datetime);
    }

}