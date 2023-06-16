<?php

namespace obbz\yii2\admin\models\base;

use Yii;
use obbz\yii2\utils\ObbzYii;

/**
* This is the model class for table "flexible_module_item".
* DO NOT MODIFY THIS FILE!
* If any changes are necessary, you must set or override the required property or method in class
*
    * @property string $id
    * @property string $user_session
    * @property integer $flexible_module_id
    * @property string $title
    * @property string $detail
    * @property string $link
    * @property string $embed_link
    * @property integer $file_size
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
*/
class FlexibleModuleItemBase extends \obbz\yii2\models\CoreActiveRecord
{
    const CACHE_PUBLISHED_ALL = 'flexible_module_item_published_all';
    const CACHE_ACTIVE_ALL = 'flexible_module_item_active_all';
    /**
    * @inheritdoc
    */
    public static function tableName()
    {
        return 'flexible_module_item';
    }


    public function rules()
    {
        return array_merge(parent::rules(),[
            [['flexible_module_id', 'file_size', 'sorting', 'create_user_id', 'modify_user_id', 'deleted_user_id', 'language_pid'], 'integer'],
            [['detail', 'key_name'], 'string'],
            [['disabled', 'deleted'], 'boolean'],
            [['created_time', 'modify_time', 'deleted_time'], 'safe'],
            [['user_session', 'title'], 'string', 'max' => 150],
            [['link', 'embed_link'], 'string', 'max' => 200],
            [['language'], 'string', 'max' => 10],
        ]);
    }




    /**
     * @inheritdoc
     * @return \obbz\yii2\admin\models\query\FlexibleModuleItemQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \obbz\yii2\admin\models\query\FlexibleModuleItemQuery(get_called_class());
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
            $t.'.flexible_module_id' => $this->flexible_module_id,
            $t.'.file_size' => $this->file_size,
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

        $query->andFilterWhere(['like', $t.'.user_session', $this->user_session])
            ->andFilterWhere(['like', $t.'.title', $this->title])
            ->andFilterWhere(['like', $t.'.detail', $this->detail])
            ->andFilterWhere(['like', $t.'.link', $this->link])
            ->andFilterWhere(['like', $t.'.embed_link', $this->embed_link])
            ->andFilterWhere(['like', $t.'.image', $this->image])
            ->andFilterWhere(['like', $t.'.language', $this->language]);
	}
    public function attributeLabels(){
        return array_merge(parent::attributeLabels(),[
                'id' => \Yii::t('model/flexible-module-item', 'ID'),
                'user_session' => \Yii::t('model/flexible-module-item', 'User Session'),
                'flexible_module_id' => \Yii::t('model/flexible-module-item', 'Flexible Module ID'),
                'title' => \Yii::t('model/flexible-module-item', 'Title'),
                'detail' => \Yii::t('model/flexible-module-item', 'Detail'),
                'link' => \Yii::t('model/flexible-module-item', 'Link'),
                'embed_link' => \Yii::t('model/flexible-module-item', 'Embed Link'),
                'file_size' => \Yii::t('model/flexible-module-item', 'File Size'),
                'image' => \Yii::t('model/flexible-module-item', 'Image'),
                'sorting' => \Yii::t('model/flexible-module-item', 'Sorting'),
                'disabled' => \Yii::t('model/flexible-module-item', 'Disabled'),
                'deleted' => \Yii::t('model/flexible-module-item', 'Deleted'),
                'created_time' => \Yii::t('model/flexible-module-item', 'Created Time'),
                'modify_time' => \Yii::t('model/flexible-module-item', 'Modify Time'),
                'deleted_time' => \Yii::t('model/flexible-module-item', 'Deleted Time'),
                'create_user_id' => \Yii::t('model/flexible-module-item', 'Create User ID'),
                'modify_user_id' => \Yii::t('model/flexible-module-item', 'Modify User ID'),
                'deleted_user_id' => \Yii::t('model/flexible-module-item', 'Deleted User ID'),
                'key_name' => \Yii::t('model/flexible-module-item', 'Key Name'),
                'language' => \Yii::t('model/flexible-module-item', 'Language'),
                'language_pid' => \Yii::t('model/flexible-module-item', 'Language Pid'),
        ]);
    }
    public function afterSave($insert, $changedAttributes)
    {
        ObbzYii::cache()->delete(self::CACHE_PUBLISHED_ALL);
        ObbzYii::cache()->delete(self::CACHE_ACTIVE_ALL);

        parent::afterSave($insert, $changedAttributes);
    }
}