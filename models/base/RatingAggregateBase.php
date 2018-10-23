<?php

namespace obbz\yii2\models\base;

use Yii;
use obbz\yii2\utils\ObbzYii;

/**
* This is the model class for table "rating_aggregate".
* DO NOT MODIFY THIS FILE!
* If any changes are necessary, you must set or override the required property or method in class
*
    * @property string $id
    * @property string $entity
    * @property integer $target_id
    * @property string $version
    * @property string $value
    * @property integer $amount
    * @property string $rating

*/
class RatingAggregateBase extends \obbz\yii2\models\CoreBaseActiveRecord
{

    const CACHE_ACTIVE_ALL = 'rating_aggregate_active_all';


/**
    * @inheritdoc
    */
    public static function tableName()
    {
        return 'rating_aggregate';
    }


    public function rules()
    {
        return array_merge(parent::rules(),[
            [['target_id', 'amount'], 'integer'],
            [['value', 'rating'], 'number'],
            [['entity'], 'string', 'max' => 50],
            [['version'], 'string', 'max' => 10],
            [['entity', 'target_id', 'version'], 'unique', 'targetAttribute' => ['entity', 'target_id', 'version']],
        ]);
    }




    /**
     * @inheritdoc
     * @return \obbz\yii2\models\query\RatingAggregateQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \obbz\yii2\models\query\RatingAggregateQuery(get_called_class());
    }
	
	/**
     * @param $query \yii\db\ActiveQuery
     */
    public function defaultQueryFilter(&$query){
        $t = self::tableName();
        $query->andFilterWhere([
            $t.'.id' => $this->id,
            $t.'.target_id' => $this->target_id,
            $t.'.value' => $this->value,
            $t.'.amount' => $this->amount,
            $t.'.rating' => $this->rating,
        ]);

        $query->andFilterWhere(['like', $t.'.entity', $this->entity])
            ->andFilterWhere(['like', $t.'.version', $this->version]);
	}

    public function attributeLabels(){
        return array_merge(parent::attributeLabels(),[
                'id' => \Yii::t('app', 'ID'),
                'entity' => \Yii::t('app', 'Entity'),
                'target_id' => \Yii::t('app', 'Target ID'),
                'version' => \Yii::t('app', 'Version'),
                'value' => \Yii::t('app', 'Value'),
                'amount' => \Yii::t('app', 'Amount'),
                'rating' => \Yii::t('app', 'Rating'),
        ]);
    }

    public function afterSave($insert, $changedAttributes)
    {
        ObbzYii::cache()->delete(self::CACHE_ACTIVE_ALL);

        parent::afterSave($insert, $changedAttributes);
    }
}