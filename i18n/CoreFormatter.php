<?php
/**
 * @author Mattakorn Limkool
 */

namespace obbz\yii2\i18n;
use obbz\yii2\models\CoreActiveRecord;
use obbz\yii2\utils\ObbzYii;
use yii\helpers\FormatConverter;
use yii\i18n\Formatter;

class CoreFormatter extends Formatter
{

    // db datetime support php format only
    const DB_DATE_FORMAT = 'Y-m-d';
    const DB_DATETIME_FORMAT = 'Y-m-d H:i:s';
    const DB_TIME_FORMAT = 'H:i:s';

//    public $timeZone = 'Asia/Bangkok'; // 'Asia/Bangkok' 'UTC'
    public $defaultTimeZone = 'Asia/Bangkok'; // 'Asia/Bangkok' 'UTC'
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

    /**
     * @param $value
     * @return int|string
     */
    public function asNumber($value, $shorten = false){
        if (empty($value)) {
            return '0';
        }
        if($shorten){
            if($value < 10000){
                return number_format($value);
            }
            else if($value < 1000000){
                return number_format($value / 1000) . 'K';
            }
            else if($value < 1000000000){
                return number_format($value / 1000000) . 'M';
            }
            else {
                // At least a billion
                $n_format = number_format($value / 1000000000) . 'B';
            }
        }else{
            return number_format($value);
        }

    }

    public function asNumberPeriod($value){
        if ($value === null || empty($value)) {
            return '<span class="number-zero">0</span>';
        }else{
            $valueText = number_format($value);
            if($value > 0){
                return '<span class="number-more-zero">'. $valueText .'</span>';
            }else{
                return '<span class="number-less-zero">'. $valueText .'</span>';
            }
        }
    }


    function asTimeAgo($date, $granularity=2) {
        // just support in 2 language, other lange fallback to en
        $language = in_array(\Yii::$app->language, ['th','th-TH','TH','TH-th']) ? 'th' : 'en';

        $date = strtotime($date);
        $difference = time() - $date;
        $periods = TimeAgoLang::$periods[$language];
        $words = TimeAgoLang::$words[$language];
        $retval = '';
        if ($difference < 5) { // less than 5 seconds ago, let's say "just now"
            $retval = $words["Just now"];
            return $retval;
        } else {
            foreach ($periods as $key => $value) {
                if ($difference >= $value) {
                    $time = floor($difference/$value);
                    $difference %= $value;
                    $retval .= ($retval ? ' ' : '').$time.' ';
                    if($language == 'en'){
                        $retval .= (($time > 1) ? $key.'s' : $key);
                    }else{
                        $retval .= (($time > 1) ? $key : $key);
                    }

                    $granularity--;
                }
                if ($granularity == '0') { break; }
            }
            return $words['When '] .$retval. $words[' ago'];
        }

    }

    /**
     * @param CoreActiveRecord[] $models - collection of model
     * @param string $attribute - attribute of model
     * @param string $glue - for separate between text
     * @return string
     */
    public function asModelImplode($models, $attribute = 'title', $glue = ' ,'){
        if ($models === null) {
            return $this->nullDisplay;
        }
        $result  = '';
        if(is_array($models)){
            foreach($models as $key => $model){
                if($key == 0){
                    $result .= $model[$attribute];
                }else{
                    $result .= $glue . $model[$attribute];
                }
            }
        }

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

//    public function asDatetime($value, $format = null){
//        if(!is_int($value)){ // convert when db value is date not timestamp
//
//        }
//        return parent::asDatetime($value, $format);
//    }

    /**
     * convert date input format from default format to db stored
     * @param $value
     * @param null $fromFormat
     * @return string
     */
    public function asDbDate($value = null, $fromFormat = null){
        if(!isset($fromFormat))
            $fromFormat = $this->dateFormat;
        return $time =  $this->timeDbFromFormat($value, 'date', $fromFormat);
    }

    /**
     * convert datetime input format from default format to db stored
     * @param $value
     * @param null $fromFormat
     * @return string
     */
    public function asDbDatetime($value = null, $fromFormat = null){
        if(!isset($fromFormat))
            $fromFormat = $this->datetimeFormat;
        return $time =  $this->timeDbFromFormat($value, 'datetime', $fromFormat);
    }

    /**
     *convert time input format from default format to db stored
     * @param $value
     * @param null $fromFormat
     * @return string
     */
    public function asDbTime($value = null, $fromFormat = null){
        if(!isset($fromFormat))
            $fromFormat = $this->timeFormat;
        return $time =  $this->timeDbFromFormat($value, 'time', $fromFormat);
    }


    public function asDefaultDateFormatToTimestamp($value){
        $format = FormatConverter::convertDateIcuToPhp($this->dateFormat);
        return \DateTime::createFromFormat($format, $value)->getTimestamp();
    }

    /**
     * @param $datetime
     * @param $type
     * @param $format
     * @return \DateTime
     */
    public function timeDbFromFormat($datetime, $type, $format){
        if (strncmp($format, 'php:', 4) === 0) {
            $format = substr($format, 4);
        } else {
            $format = FormatConverter::convertDateIcuToPhp($format, $type, $this->locale);
        }

        if($datetime === null){ // get current datetime
            $datetime = date($format);
        }

        $utcDatetime = \DateTime::createFromFormat($format, $datetime);
//        ObbzYii::debug($utcDatetime);
        if($type == "date"){
            $dbFormat = self::DB_DATE_FORMAT;
        }else if($type == "datetime"){
            $dbFormat = self::DB_DATETIME_FORMAT;
        }else{
            $dbFormat = self::DB_TIME_FORMAT;
        }
//        $dbFormat = self::DB_DATETIME_FORMAT;
//        ObbzYii::debug($utcDatetime);
        return $utcDatetime->format($dbFormat);
//        echo $format . ' ' . $datetime;
//        return \DateTime::createFromFormat($format, $datetime, new \DateTimeZone('UTC'));
    }







    /**
     * convert bootstrap yii2 datetime format datetime picker
     * http://www.malot.fr/bootstrap-datetimepicker/#options
     *
     *  p : meridian in lower case ('am' or 'pm') - according to locale file
     *  P : meridian in upper case ('AM' or 'PM') - according to locale file
     *  s : seconds without leading zeros
     *  ss : seconds, 2 digits with leading zeros
     *  i : minutes without leading zeros
     *  ii : minutes, 2 digits with leading zeros
     *  h : hour without leading zeros - 24-hour format
     *  hh : hour, 2 digits with leading zeros - 24-hour format
     *  H : hour without leading zeros - 12-hour format
     *  HH : hour, 2 digits with leading zeros - 12-hour format
     *  d : day of the month without leading zeros
     *  dd : day of the month, 2 digits with leading zeros
     *  m : numeric representation of month without leading zeros
     *  mm : numeric representation of the month, 2 digits with leading zeros
     *  M : short textual representation of a month, three letters
     *  MM : full textual representation of a month, such as January or March
     *  yy : two digit representation of a year
     *  yyyy : full numeric representation of a year, 4 digits
     *
     *
     * @param $format
     */
    public function convertDateYiiToBsDatepicker($pattern){
        // todo - implement exactly formate
        if (strncmp($pattern, 'php:', 4) === 0) {
            $pattern = substr($pattern, 4);
            $pattern = FormatConverter::convertDatePhpToIcu($pattern);
        }
        return strtr($pattern, [
            'G' => '',      // era designator like (Anno Domini)
            'Y' => '',      // 4digit year of "Week of Year"
            'y' => '',    // 4digit year e.g. 2014
            'yyyy' => 'yyyy', // 4digit year e.g. 2014
            'yy' => 'yy',    // 2digit year number eg. 14
            'u' => '',      // extended year e.g. 4601
            'U' => '',      // cyclic year name, as in Chinese lunar calendar
            'r' => '',      // related Gregorian year e.g. 1996
            'Q' => '',      // number of quarter
            'QQ' => '',     // number of quarter '02'
            'QQQ' => '',    // quarter 'Q2'
            'QQQQ' => '',   // quarter '2nd quarter'
            'QQQQQ' => '',  // number of quarter '2'
            'q' => '',      // number of Stand Alone quarter
            'qq' => '',     // number of Stand Alone quarter '02'
            'qqq' => '',    // Stand Alone quarter 'Q2'
            'qqqq' => '',   // Stand Alone quarter '2nd quarter'
            'qqqqq' => '',  // number of Stand Alone quarter '2'
            'M' => 'm',    // Numeric representation of a month, without leading zeros
            'MM' => 'mm',   // Numeric representation of a month, with leading zeros
            'MMM' => 'M',   // A short textual representation of a month, three letters
            'MMMM' => 'MM', // A full textual representation of a month, such as January or March
            'MMMMM' => '',  //
            'L' => '',     // Stand alone month in year
            'LL' => '',   // Stand alone month in year
            'LLL' => '',   // Stand alone month in year
            'LLLL' => '', // Stand alone month in year
            'LLLLL' => '',  // Stand alone month in year
            'w' => '',      // ISO-8601 week number of year
            'ww' => '',     // ISO-8601 week number of year
            'W' => '',      // week of the current month
            'd' => 'd',     // day without leading zeros
            'dd' => 'dd',   // day with leading zeros
            'D' => '',     // day of the year 0 to 365
            'F' => '',      // Day of Week in Month. eg. 2nd Wednesday in July
            'g' => '',      // Modified Julian day. This is different from the conventional Julian day number in two regards.
            'E' => '',     // day of week written in short form eg. Sun
            'EE' => '',
            'EEE' => '',
            'EEEE' => '', // day of week fully written eg. Sunday
            'EEEEE' => '',
            'EEEEEE' => '',
            'e' => '',      // ISO-8601 numeric representation of the day of the week 1=Mon to 7=Sun
            'ee' => '',     // php 'w' 0=Sun to 6=Sat isn't supported by ICU -> 'w' means week number of year
            'eee' => '',
            'eeee' => '',
            'eeeee' => '',
            'eeeeee' => '',
            'c' => '',      // ISO-8601 numeric representation of the day of the week 1=Mon to 7=Sun
            'cc' => '',     // php 'w' 0=Sun to 6=Sat isn't supported by ICU -> 'w' means week number of year
            'ccc' => '',
            'cccc' => '',
            'ccccc' => '',
            'cccccc' => '',
            'a' => 'p',      // am/pm marker
            'h' => 'H',      // 12-hour format of an hour without leading zeros 1 to 12h
            'hh' => 'HH',     // 12-hour format of an hour with leading zeros, 01 to 12 h
            'H' => 'h',      // 24-hour format of an hour without leading zeros 0 to 23h
            'HH' => 'hh',     // 24-hour format of an hour with leading zeros, 00 to 23 h
            'k' => '',      // hour in day (1~24)
            'kk' => '',     // hour in day (1~24)
            'K' => '',      // hour in am/pm (0~11)
            'KK' => '',     // hour in am/pm (0~11)
            'm' => 'i',      // Minutes without leading zeros, not supported by php but we fallback
            'mm' => 'ii',     // Minutes with leading zeros
            's' => 's',      // Seconds, without leading zeros, not supported by php but we fallback
            'ss' => 'ss',     // Seconds, with leading zeros
            'S' => '',      // fractional second
            'SS' => '',     // fractional second
            'SSS' => '',    // fractional second
            'SSSS' => '',   // fractional second
            'A' => '',      // milliseconds in day
            'z' => '',      // Timezone abbreviation
            'zz' => '',     // Timezone abbreviation
            'zzz' => '',    // Timezone abbreviation
            'zzzz' => '',   // Timzone full name, not supported by php but we fallback
            'Z' => '',      // Difference to Greenwich time (GMT) in hours
            'ZZ' => '',     // Difference to Greenwich time (GMT) in hours
            'ZZZ' => '',    // Difference to Greenwich time (GMT) in hours
            'ZZZZ' => '',   // Time Zone: long localized GMT (=OOOO) e.g. GMT-08:00
            'ZZZZZ' => '',  //  TIme Zone: ISO8601 extended hms? (=XXXXX)
            'O' => '',      // Time Zone: short localized GMT e.g. GMT-8
            'OOOO' => '',   //  Time Zone: long localized GMT (=ZZZZ) e.g. GMT-08:00
            'v' => '',      // Time Zone: generic non-location (falls back first to VVVV and then to OOOO) using the ICU defined fallback here
            'vvvv' => '',   // Time Zone: generic non-location (falls back first to VVVV and then to OOOO) using the ICU defined fallback here
            'V' => '',      // Time Zone: short time zone ID
            'VV' => '',     // Time Zone: long time zone ID
            'VVV' => '',    // Time Zone: time zone exemplar city
            'VVVV' => '',   // Time Zone: generic location (falls back to OOOO) using the ICU defined fallback here
            'X' => '',      // Time Zone: ISO8601 basic hm?, with Z for 0, e.g. -08, +0530, Z
            'XX' => '',     // Time Zone: ISO8601 basic hm, with Z, e.g. -0800, Z
            'XXX' => '',    // Time Zone: ISO8601 extended hm, with Z, e.g. -08:00, Z
            'XXXX' => '',   // Time Zone: ISO8601 basic hms?, with Z, e.g. -0800, -075258, Z
            'XXXXX' => '',  // Time Zone: ISO8601 extended hms?, with Z, e.g. -08:00, -07:52:58, Z
            'x' => '',      // Time Zone: ISO8601 basic hm?, without Z for 0, e.g. -08, +0530
            'xx' => '',     // Time Zone: ISO8601 basic hm, without Z, e.g. -0800
            'xxx' => '',    // Time Zone: ISO8601 extended hm, without Z, e.g. -08:00
            'xxxx' => '',   // Time Zone: ISO8601 basic hms?, without Z, e.g. -0800, -075258
            'xxxxx' => '',  // Time Zone: ISO8601 extended hms?, without Z, e.g. -08:00, -07:52:58
        ]);
    }

}