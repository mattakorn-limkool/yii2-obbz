<?php

namespace obbz\yii2\models\base;

use Yii;
use obbz\yii2\utils\ObbzYii;

/**
* This is the model class for table "rating".
* DO NOT MODIFY THIS FILE!
* If any changes are necessary, you must set or override the required property or method in class
*
    * @property string $id
    * @property string $entity
    * @property string $target_id
    * @property string $user_id
    * @property string $user_ip
    * @property string $version
    * @property string $value
    * @property integer $created_at

*/
class RatingBase extends \obbz\yii2\models\CoreBaseActiveRecord
{

    const CACHE_ACTIVE_ALL = 'rating_active_all';


/**
    * @inheritdoc
    */
    public static function tableName()
    {
        return 'rating';
    }


    public function rules()
    {
        return array_merge(parent::rules(),[
            [['target_id', 'user_id', 'created_at'], 'integer'],
            [['value'], 'number'],
            [['entity'], 'string', 'max' => 50],
            [['user_ip'], 'string', 'max' => 39],
            [['version'], 'string', 'max' => 10],
        ]);
    }




    /**
     * @inheritdoc
     * @return \obbz\yii2\models\query\RatingQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \obbz\yii2\models\query\RatingQuery(get_called_class());
    }
	
	/**
     * @param $query \yii\db\ActiveQuery
     */
    public function defaultQueryFilter(&$query){
        $t = self::tableName();
        $query->andFilterWhere([
            $t.'.id' => $this->id,
            $t.'.target_id' => $this->target_id,
            $t.'.user_id' => $this->user_id,
            $t.'.value' => $this->value,
            $t.'.created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', $t.'.entity', $this->entity])
            ->andFilterWhere(['like', $t.'.user_ip', $this->user_ip])
            ->andFilterWhere(['like', $t.'.version', $this->version]);
	}

    public function attributeLabels(){
        return array_merge(parent::attributeLabels(),[
                'id' => \Yii::t('app', 'ID'),
                'entity' => \Yii::t('app', 'Entity'),
                'target_id' => \Yii::t('app', 'Target ID'),
                'user_id' => \Yii::t('app', 'User ID'),
                'user_ip' => \Yii::t('app', 'User Ip'),
                'version' => \Yii::t('app', 'Version'),
                'value' => \Yii::t('app', 'Value'),
                'created_at' => \Yii::t('app', 'Created At'),
        ]);
    }

    public function afterSave($insert, $changedAttributes)
    {
        ObbzYii::cache()->delete(self::CACHE_ACTIVE_ALL);

        parent::afterSave($insert, $changedAttributes);
    }
}