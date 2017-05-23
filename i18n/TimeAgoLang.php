<?php
namespace obbz\yii2\i18n;
/**
 * @author: Mattakorn Limkool
 *
 */
class TimeAgoLang
{
    public static $periods = [
        'en'=>[
            'decade' => 315360000,
            'year' => 31536000,
            'month' => 2628000,
            'week' => 604800,
            'day' => 86400,
            'hour' => 3600,
            'minute' => 60,
            'second' => 1
        ],
        'th' =>[
            'ทศวรรษ' => 315360000,
            'ปี' => 31536000,
            'เดือน' => 2628000,
            'สัปดาห์' => 604800,
            'วัน' => 86400,
            'ชั่วโมง' => 3600,
            'นาที' => 60,
            'วินาที' => 1
        ]
    ];

    public static $words = [
        'en' => [
            'Just now' => 'Just now',
            'When ' => 'When ',
            ' ago' => ' ago',
        ],
        'th' => [
            'Just now' => 'เมื่อซักครู่นี้',
            'When ' => 'เมื่อ ',
            ' ago' => 'ที่แล้ว',
        ]
    ];


}