<?php

namespace obbz\yii2\models;

use obbz\yii2\models\base\RatingBase;
use obbz\yii2\utils\ObbzYii;
use yii\behaviors\TimestampBehavior;

/**
*/


class Rating extends RatingBase
{
    public $countRecord;

    public function rules(){
        return array_merge(parent::rules(),[
            //[['field'], 'required', 'on'=>$this->scenarioCU()],
        ]);
    }

	public function behaviors(){
        return array_merge(parent::behaviors(),[
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false,
            ],
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

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
           // your code here
            $this->user_ip = ObbzYii::getIpAddress();
            return true;
        } else {
            return false;
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        // your code here
        $this->updateAggregate();
        parent::afterSave($insert, $changedAttributes);
    }
    public function afterDelete()
    {
        parent::afterDelete();
        $this->updateAggregate();
     }

//    public function afterFind()
//    {
//        parent::afterFind();
//        // your code here
//
//    }

    public function updateAggregate(){
        $sumModel = self::find()->select("SUM(value) as value, COUNT(*) as countRecord")->where([
            'entity'=>$this->entity,
            'target_id'=>$this->target_id,
            'version' => $this->version
        ])->one();

        $modelAggregate = RatingAggregate::find()->where([
            'entity'=>$this->entity,
            'target_id'=>$this->target_id,
            'version' => $this->version
        ])->one();

        // update aggreate
        if(!$modelAggregate){
            $modelAggregate = new RatingAggregate();
            $modelAggregate->entity = $this->entity;
            $modelAggregate->target_id = $this->target_id;
            $modelAggregate->version = $this->version;
        }
        if($sumModel && $sumModel->countRecord == 0){
            $modelAggregate->amount = 0;
            $modelAggregate->value = 0;
            $modelAggregate->rating = 0;
        }else{
            $modelAggregate->amount = $sumModel->countRecord;
            $modelAggregate->value = $sumModel->value;
            $modelAggregate->rating = $modelAggregate->value / $modelAggregate->amount;
        }

        if(!$modelAggregate->save()){
            ObbzYii::debug($modelAggregate->getFirstErrors());
        }
    }
}