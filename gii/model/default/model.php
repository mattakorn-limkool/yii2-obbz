<?php
/** Obbz core model */
/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\model\Generator */
/* @var $tableName string full table name */
/* @var $className string class name */
/* @var $queryClassName string query class name */
/* @var $tableSchema yii\db\TableSchema */
/* @var $labels string[] list of attribute labels (name => label) */
/* @var $rules string[] list of validation rules */
/* @var $relations array list of relations (name => relation declaration) */

echo "<?php\n";
?>

namespace <?= $generator->ns ?>;

use obbz\yii2\utils\ObbzYii;
/**
<?php if (!empty($relations)): ?>

<?php foreach ($relations as $name => $relation): ?>* @property <?= $relation[1] . ($relation[2] ? '[]' : '') . ' $' . lcfirst($name) . "\n" ?><?php endforeach; ?><?php endif; ?>*/

class <?= $className ?> extends <?= '\\'.$generator->ns.'\\base\\'.$className.'Base' . "\n" ?>
{

    public function rules(){
        return array_merge(parent::rules(),[
			['image', 'file', 'extensions' => 'jpg, jpeg', 'maxSize' => \Yii::$app->params['upload.maxSize'], 'on'=>$this->scenarioCU()],
            //[['field'], 'required', 'on'=>$this->scenarioCU()],
        ]);
    }

	public function behaviors(){
        return array_merge(parent::behaviors(),[
			$this->defaultImgBehavior('image', [
                    'thumb'=> ['width'=>300, 'quality' => 100]
                ], ['scenarios' => $this->scenarioCU()]) ,
			// other behavior
        ]);
    }

    public function attributeLabels(){
        return array_merge(parent::attributeLabels(),[]);
    }
	
<?php foreach ($relations as $name => $relation): ?>

    /**
    * @return \yii\db\ActiveQuery
    */
    public function get<?= $name ?>()
    {
        <?= $relation[0] . "\n" ?>
    }
<?php endforeach; ?>
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

//    public function afterFind($insert, $changedAttributes)
//    {
//        parent::afterFind();
//        // your code here
//
//    }

   
}