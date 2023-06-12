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

use obbz\yii2\utils\ArrayHelper;
use obbz\yii2\utils\ObbzYii;
/**
<?php if (!empty($relations)): ?>

<?php foreach ($relations as $name => $relation): ?>* @property <?= $relation[1] . ($relation[2] ? '[]' : '') . ' $' . lcfirst($name) . "\n" ?><?php endforeach; ?><?php endif; ?>*/

class <?= $className ?> extends <?= '\\'.$generator->ns.'\\base\\'.$className.'Base' . "\n" ?>
{
    const DEFAULT_THUMBS = [
        'thumb'=> ['width'=>300]
    ];

    public $autoDateFields = [
//        ['field' =>'created_time', 'inputType'=>self::AUTODATE_TYPE_DATETIME, 'scenarios'=>[self::SCENARIO_BE_CREATE, self::SCENARIO_BE_UPDATE]],
//        ['field' =>'modify_time', 'inputType'=>self::AUTODATE_TYPE_DATETIME, 'scenarios'=>[self::SCENARIO_BE_CREATE, self::SCENARIO_BE_UPDATE]],
    ];

//    public function scenarioCreate(){
//        return array_merge(parent::scenarioCreate(), []);
//    }
//
//    public function scenarioUpdate(){
//        return array_merge(parent::scenarioUpdate(), []);
//    }


public function rules(){
        $thumbWidth = ArrayHelper::getValue(self::DEFAULT_THUMBS, 'thumb.width');
        $thumbHeight = ArrayHelper::getValue(self::DEFAULT_THUMBS, 'thumb.height');
        return array_merge(parent::rules(),[
			['image', 'image', 'extensions' => 'jpg, jpeg',
                'maxSize' => \Yii::$app->params['upload.maxSize'],
                //'minWidth'=> $thumbWidth, 'minHeight' => $thumbHeight,
                'on'=>$this->scenarioCU()],
            //[['field'], 'required', 'on'=>$this->scenarioCU()],
        ]);
    }

	public function behaviors(){
        return array_merge(parent::behaviors(),[
			'uploadImage' => $this->defaultImgBehavior('image', self::DEFAULT_THUMBS, ['scenarios' => $this->scenarioCU()]) ,
//            'translateable' => [
//                'class' => \obbz\yii2\behaviors\TranslationBehavior::class,
//                'translationAttributes' => ['title','detail'],
//            ],
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
//	 public function beforeValidate() {
//        if(parent::beforeValidate()) {
//            // your code here
//            return true;
//        }else{
//            return false;
//        }
//    }

//    public function afterValidate(){
//        // your code here
//        parent::afterValidate();
//    }

//    public function beforeSave($insert){
//        if (parent::beforeSave($insert)) {
//           // your code here
//            return true;
//        } else {
//            return false;
//        }
//    }

//    public function afterSave($insert, $changedAttributes){
//        // your code here
//        parent::afterSave($insert, $changedAttributes);
//    }

//    public function afterFind(){
//        parent::afterFind();
//        // your code here
//
//    }

   
}