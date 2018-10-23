<?php
/**
 * Created by PhpStorm.
 * User: mattakorn
 * Date: 21/2/2560
 * Time: 19:50
 */

namespace obbz\yii2\models;

use common\models\User;
use obbz\yii2\utils\ObbzYii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;


/**
 * This is the core model class for default table
 * @property integer $id
 * @property string $title
 * @property string $detail
 * @property string $img
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
 *
 * core relations
 * @property User $createdUser
 * @property User $updatedUser
 * @property User $deletedUser
 */
class CoreActiveRecord extends CoreBaseActiveRecord
{
    public $statusPublish;

    /**
     * default rules for core model
     * @return array
     */

    public function behaviors()
    {
        return [
            // for sortable grid
            'sortable' => [
                'class' => \kotchuprik\sortable\behaviors\Sortable::class,
                'query' => self::find(),
                'orderAttribute'=>'sorting',
            ],


        ];
    }
    public function attributeLabels(){
        return [
            'statusPublish' => \Yii::t('obbz', 'Publish Status'),
        ];
    }

    public function beforeValidate() {
        if(parent::beforeValidate()) {
            #region core
//            ObbzYii::debug( $this->scenarioCreate() + $this->scenarioUpdate());
            $checkScenario = $this->scenarioCU();
            if($this->isScenario($checkScenario)){

                $this->deleted = ObbzYii::getValue($this, 'deleted', 0);
                $this->disabled = ObbzYii::getValue($this, 'disabled', 0);
//                $this->sorting = ObbzYii::getValue($this, 'sorting', 99999);

                $userId = ObbzYii::user()->getId();
                if($this->isNewRecord){
                    $this->created_time = ObbzYii::formatter()->asDbDatetime();
                    if(!empty($userId))
                        $this->create_user_id = $userId;
                }else{
                    $this->modify_time = ObbzYii::formatter()->asDbDatetime();
                    if(!empty($userId))
                        $this->modify_user_id = $userId;
                }
            }

            #endregion
            return true;
        }else{
            return false;
        }
    }



    /**
     * auto set logging for user change record
     * @param bool $insert
     * @return bool
     */
//    public function beforeSave($insert)
//    {
//        if (parent::beforeSave($insert)) {
//            #region core
//            $userId = ObbzYii::user()->getId();
//            if($this->isNewRecord){
//                $this->created_time = ObbzYii::dateDb(null, 'datetime');
//                if(!empty($userId))
//                    $this->create_user_id = $userId;
//            }else{
//                $this->modify_time = ObbzYii::dateDb(null, 'datetime');
//                if(!empty($userId))
//                    $this->modify_user_id = $userId;
//            }
//            #endregion
//
//            return true;
//        } else {
//            return false;
//        }
//    }

    /**
     * Mark this record as publish
     * @return bool
     */
    public function markPublish(){
        $this->disabled = false;
        return $this->save(false, ['disabled']);
    }

    /**
     * Mark this record as unpublish
     * @return bool
     */
    public function markUnpublish(){
        $this->disabled = true;
        return $this->save(false, ['disabled']);
    }

    /**
     * Mark this record as deleted
     * @return bool
     */
    public function markDelete(){
        $userId = ObbzYii::user()->getId();

        $this->deleted = true;
        $this->deleted_time = ObbzYii::formatter()->asDbDatetime();
        if(!empty($userId))
            $this->deleted_user_id = $userId;

        return $this->save(false, ['deleted', 'deleted_time', 'deleted_user_id']);
    }

    /**
     * Mark this record as active (not deleted)
     * @return bool
     */
    public function markActive(){
        $this->deleted = 0;
        return $this->save(false, ['deleted']);
    }

    /**
     * check record has published
     * @return bool
     */
    public function hasPublished(){
        return $this->disabled ? false: true;
    }

    /**
     * check record has unpublished
     * @return bool
     */
    public function hasUnpublished(){
        return !$this->hasPublished();
    }
    /**
     * check record has active
     * @return bool
     */
    public function hasActive(){
        return !$this->hasDeleted();
    }
    /**
     * check record has deleted
     * @return bool
     */
    public function hasDeleted(){
        return $this->deleted ? true: false;
    }

    public function prepareCoreAttributesFilter(){
        $this->disabled = $this->disabled === "" || !isset($this->disabled) ? "": (int)$this->disabled;
        $this->deleted = $this->deleted === "" || !isset($this->deleted) ? "": (int)$this->deleted;
    }

    /**
     * @param $model CoreActiveRecord
     * @return string
     */
    public function displayPublishStatus($showHtml = true){
        $list = CoreDataList::statusPublish();
        $label =  ArrayHelper::getValue($list, $this->disabled);
        $status = $this->hasPublished() ? \Yii::t('obbz', 'Published') : \Yii::t('obbz', 'Unpublished');
        return $showHtml ? Html::tag('span' , $label, ['class'=>'core-grid-status-' .  $status ]): $label;
    }

    public function getCoreAttributes(){
        return self::attributes();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedUser()
    {
        return $this->hasOne(User::class, ['id' => 'create_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedUser()
    {
        return $this->hasOne(User::class, ['id' => 'modify_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDeletedUser()
    {
        return $this->hasOne(User::class, ['id' => 'deleted_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTranslations()
    {
        return $this->hasMany(self::calledClass(), ['language_pid' => 'id']);
    }


}

