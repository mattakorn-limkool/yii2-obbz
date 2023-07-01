<?php

namespace obbz\yii2\admin\models\base;

use Yii;
use obbz\yii2\utils\ObbzYii;

/**
* This is the model class for table "flexible_module".
* DO NOT MODIFY THIS FILE!
* If any changes are necessary, you must set or override the required property or method in class
*
    * @property string $id
    * @property string $title
    * @property string $detail
    * @property string $config
    * @property string $column_pattern
    * @property string $section
    * @property string $ref_model_id
    * @property string $image
    * @property integer $sorting
    * @property boolean $disabled
    * @property boolean $deleted
    * @property string $created_time
    * @property string $modify_time
    * @property string $deleted_time
    * @property integer $create_user_id
    * @property integer $modify_user_id
    * @property integer $deleted_user_id
    * @property string $key_name
    * @property string $language
    * @property integer $language_pid
    * @property string $custom_1
    * @property string $custom_2
    * @property string $custom_3
    * @property string $custom_4
    * @property string $custom_5
*/
class FlexibleModuleBase extends \obbz\yii2\models\CoreActiveRecord
{
    const CACHE_PUBLISHED_ALL = 'flexible_module_published_all';
    const CACHE_ACTIVE_ALL = 'flexible_module_active_all';
    /**
    * @inheritdoc
    */
    public static function tableName()
    {
        return 'flexible_module';
    }


    public function rules()
    {
        return array_merge(parent::rules(),[
            [['detail', 'config', 'key_name', 'custom_1', 'custom_2', 'custom_3', 'custom_4', 'custom_5'], 'string'],
            [['ref_model_id', 'sorting', 'create_user_id', 'modify_user_id', 'deleted_user_id', 'language_pid'], 'integer'],
            [['disabled', 'deleted'], 'boolean'],
            [['created_time', 'modify_time', 'deleted_time'], 'safe'],
            [['title', 'section'], 'string', 'max' => 150],
            [['column_pattern'], 'string', 'max' => 100],
            [['language'], 'string', 'max' => 10],
        ]);
    }




    /**
     * @inheritdoc
     * @return \obbz\yii2\admin\models\query\FlexibleModuleQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \obbz\yii2\admin\models\query\FlexibleModuleQuery(get_called_class());
    }
	
	/**
     * @param $query \yii\db\ActiveQuery
     */
    public function defaultQueryFilter(&$query){
        $t = self::tableName();
		// grid filtering conditions
        $this->prepareCoreAttributesFilter();

        $query->andFilterWhere([
            $t.'.id' => $this->id,
            $t.'.ref_model_id' => $this->ref_model_id,
            $t.'.sorting' => $this->sorting,
            $t.'.disabled' => $this->disabled,
            $t.'.deleted' => $this->deleted,
            $t.'.created_time' => $this->created_time,
            $t.'.modify_time' => $this->modify_time,
            $t.'.deleted_time' => $this->deleted_time,
            $t.'.create_user_id' => $this->create_user_id,
            $t.'.modify_user_id' => $this->modify_user_id,
            $t.'.deleted_user_id' => $this->deleted_user_id,
            $t.'.key_name' => $this->key_name,
            $t.'.language_pid' => $this->language_pid,
        ]);

        $query->andFilterWhere(['like', $t.'.title', $this->title])
            ->andFilterWhere(['like', $t.'.detail', $this->detail])
            ->andFilterWhere(['like', $t.'.config', $this->config])
            ->andFilterWhere(['like', $t.'.column_pattern', $this->column_pattern])
            ->andFilterWhere(['like', $t.'.section', $this->section])
            ->andFilterWhere(['like', $t.'.image', $this->image])
            ->andFilterWhere(['like', $t.'.language', $this->language])
            ->andFilterWhere(['like', $t.'.custom_1', $this->custom_1])
            ->andFilterWhere(['like', $t.'.custom_2', $this->custom_2])
            ->andFilterWhere(['like', $t.'.custom_3', $this->custom_3])
            ->andFilterWhere(['like', $t.'.custom_4', $this->custom_4])
            ->andFilterWhere(['like', $t.'.custom_5', $this->custom_5]);
	}
    public function attributeLabels(){
        return array_merge(parent::attributeLabels(),[
                'id' => \Yii::t('model/flexible-module', 'ID'),
                'title' => \Yii::t('model/flexible-module', 'Title'),
                'detail' => \Yii::t('model/flexible-module', 'Detail'),
                'config' => \Yii::t('model/flexible-module', 'Config'),
                'column_pattern' => \Yii::t('model/flexible-module', 'Column Pattern'),
                'section' => \Yii::t('model/flexible-module', 'Section'),
                'ref_model_id' => \Yii::t('model/flexible-module', 'Ref Model ID'),
                'image' => \Yii::t('model/flexible-module', 'Image'),
                'sorting' => \Yii::t('model/flexible-module', 'Sorting'),
                'disabled' => \Yii::t('model/flexible-module', 'Disabled'),
                'deleted' => \Yii::t('model/flexible-module', 'Deleted'),
                'created_time' => \Yii::t('model/flexible-module', 'Created Time'),
                'modify_time' => \Yii::t('model/flexible-module', 'Modify Time'),
                'deleted_time' => \Yii::t('model/flexible-module', 'Deleted Time'),
                'create_user_id' => \Yii::t('model/flexible-module', 'Create User ID'),
                'modify_user_id' => \Yii::t('model/flexible-module', 'Modify User ID'),
                'deleted_user_id' => \Yii::t('model/flexible-module', 'Deleted User ID'),
                'key_name' => \Yii::t('model/flexible-module', 'Key Name'),
                'language' => \Yii::t('model/flexible-module', 'Language'),
                'language_pid' => \Yii::t('model/flexible-module', 'Language Pid'),
                'custom_1' => \Yii::t('model/flexible-module', 'Custom 1'),
                'custom_2' => \Yii::t('model/flexible-module', 'Custom 2'),
                'custom_3' => \Yii::t('model/flexible-module', 'Custom 3'),
                'custom_4' => \Yii::t('model/flexible-module', 'Custom 4'),
                'custom_5' => \Yii::t('model/flexible-module', 'Custom 5'),
        ]);
    }
    public function afterSave($insert, $changedAttributes)
    {
        ObbzYii::cache()->delete(self::CACHE_PUBLISHED_ALL);
        ObbzYii::cache()->delete(self::CACHE_ACTIVE_ALL);

        parent::afterSave($insert, $changedAttributes);
    }
}