<?php
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

class <?= $className ?> extends <?= '\\'.$generator->ns.'\\base\\'.$className.'Base' . "\n" ?>
{

    public function rules(){
        return array_merge(parent::rules(),[
			['image', 'file', 'extensions' => 'jpg, png', 'on'=>array_merge($this->scenarioUpdate(), $this->scenarioCreate())],
        ]);
    }

	public function behaviors(){
        return array_merge(parent::behaviors(),[
			$this->defaultImgBehavior('image', [
                    'thumb'=> ['width'=>300, 'quality' => 100]
                ], ['scenarios' => array_merge($this->scenarioUpdate(), $this->scenarioCreate())]) ,
			// other behavior
        ]);
    }

    public function attributeLabels(){
        return array_merge(parent::attributeLabels(),[
        <?php foreach ($labels as $name => $label): ?>
            <?= "'$name' => " . $generator->generateString($label) . ",\n" ?>
        <?php endforeach; ?>
        ]);
    }


   
}