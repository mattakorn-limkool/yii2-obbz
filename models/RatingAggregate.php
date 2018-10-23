<?php

namespace obbz\yii2\models;

use obbz\yii2\models\base\RatingAggregateBase;
use obbz\yii2\utils\ObbzYii;
/**
*/


class RatingAggregate extends RatingAggregateBase
{

    public function rules(){
        return array_merge(parent::rules(),[
            //[['field'], 'required', 'on'=>$this->scenarioCU()],
        ]);
    }

	public function behaviors(){
        return array_merge(parent::behaviors(),[
			
        ]);
    }

    public function attributeLabels(){
        return array_merge(parent::attributeLabels(),[]);
    }
	

//	public function beforeValidate() {
//        if(parent::beforeValidate()) {
//            // your code here
//            return true;
//        }else{
//            return false;
//        }
//    }

//    public function afterValidate()
//    {
//        // your code here
//        parent::afterValidate();
//    }

//    public function beforeSave($insert)
//    {
//        if (parent::beforeSave($insert)) {
//           // your code here
//            return true;
//        } else {
//            return false;
//        }
//    }

//    public function afterSave($insert, $changedAttributes)
//    {
//        // your code here
//        parent::afterSave($insert, $changedAttributes);
//    }

//    public function afterFind()
//    {
//        parent::afterFind();
//        // your code here
//
//    }
}