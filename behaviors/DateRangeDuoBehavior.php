<?php

namespace obbz\yii2\behaviors;

use obbz\yii2\utils\ObbzYii;
use yii\base\Model;
use yii\base\Behavior;
use yii\base\InvalidParamException;
use yii\base\InvalidConfigException;
use yii\helpers\FormatConverter;

class DateRangeDuoBehavior extends Behavior
{

    public $startAttribute;

    public $startTimestampAttribute;

    public $endAttribute;

    public $endTimestampAttribute;

    /** @var  input date/datetime format */
    public $dateFormat;

    /** @var bool input format is date only */
    public $dateOnly = true;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (!isset($this->startAttribute)) {
            throw new InvalidConfigException('The "startAttribute" property must be specified.');
        }
        if (!isset($this->endAttribute)) {
            throw new InvalidConfigException('The "endAttribute" property must be specified.');
        }

        if (!isset($this->startTimestampAttribute)) {
            throw new InvalidConfigException('The "startTimestampAttribute" property must be specified.');
        }
        if (!isset($this->endTimestampAttribute)) {
            throw new InvalidConfigException('The "endTimestampAttribute" property must be specified.');
        }


    }

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            Model::EVENT_AFTER_VALIDATE => 'afterValidate',
        ];
    }

    /**
     * Handles owner 'afterValidate' event.
     * @param \yii\base\Event $event event instance.
     */
    public function afterValidate($event)
    {
        if ($this->owner->hasErrors() || $event->name != Model::EVENT_AFTER_VALIDATE) {
            return;
        }

        if(!empty($this->owner->{$this->startAttribute})){
            $startDateValue = $this->owner->{$this->startAttribute};
            if($this->dateOnly){
                $this->setOwnerAttribute($this->startTimestampAttribute, $startDateValue . " 00:00:00");
            }else{
                $this->setOwnerAttribute($this->startTimestampAttribute, $startDateValue);
            }

        }

        if(!empty($this->owner->{$this->endAttribute})){
            $endDateValue = $this->owner->{$this->endAttribute};
            if($this->dateOnly){
                $this->setOwnerAttribute($this->endTimestampAttribute, $endDateValue . " 23:59:59");
            }else{
                $this->setOwnerAttribute($this->endTimestampAttribute, $endDateValue);
            }

        }


    }

    /**
     * Evaluates the attribute value and assigns it to the given attribute.
     * @param string $attribute the owner attribute name
     * @param string $date a date string
     */
    protected function setOwnerAttribute($attribute, $date)
    {
        $timestamp = static::dateToTime($date);
        $this->owner->$attribute = $timestamp;
//        if ($dateFormat === false) {
//            $this->owner->$attribute = $date;
//        } else {
//            $timestamp = static::dateToTime($date);
//            if ($dateFormat === null) {
//                $this->owner->$attribute = $timestamp;
//            } else {
//                $this->owner->$attribute = $timestamp !== false ? date($dateFormat, $timestamp) : false;
//            }
//        }
    }

    /**
     * Parses the given date into a Unix timestamp.
     * @param string $date a date string
     * @return integer|false a Unix timestamp. False on failure.
     */
    protected static function dateToTime($date)
    {

        $format = FormatConverter::convertDateIcuToPhp(ObbzYii::formatter()->datetimeFormat);
        return \DateTime::createFromFormat($format, $date)->getTimestamp();
//        return strtotime($date);
    }
}
