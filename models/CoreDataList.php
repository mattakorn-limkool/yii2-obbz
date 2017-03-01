<?php
/**
 * Created by PhpStorm.
 * User: mattakorn
 * Date: 24/2/2560
 * Time: 3:01
 */

namespace obbz\yii2\models;


use obbz\yii2\utils\ObbzYii;

class CoreDataList
{
    /**
     * list of publish by disable field
     * @return array
     */
    public static function statusPublish(){
        return [
            0 => ObbzYii::t('Published'),
            1 => ObbzYii::t('Unpublished'),
        ];
    }

    /**
     * list of delete by deleted field
     * @return array
     */
    public static function statusDelete(){
        return [
            0 => ObbzYii::t('Active'),
            1 => ObbzYii::t('Deleted'),
        ];
    }
}