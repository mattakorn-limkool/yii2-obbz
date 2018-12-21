<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\utils;

use PA\ProvinceTh\Factory;
use yii\helpers\ArrayHelper;

class Thailand extends Factory
{
    public static function provinceList($language = 'th')
    {
        $provinces = self::province()->toArray();
        $field = ($language == 'th') ? 'name_th' : 'name_en';
        return ArrayHelper::map($provinces, 'id', $field);
    }

    public static function provinceValueById($id, $language = 'th'){
        $provinces = self::province()->toArray();
        $field = ($language == 'th') ? 'name_th' : 'name_en';
        foreach($provinces as $province){
            if($province['id'] == $id){
                return $province[$field];
            }
        }

        return null;
    }
}