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
			
        ]);
    }

	public function behaviors(){
        return array_merge(parent::behaviors(),[
			
        ]);
    }

    public function attributeLabels(){
        return array_merge(parent::attributeLabels(),[
        <?php foreach ($labels as $name => $label): ?>
        <?= "'$name' => " . $generator->generateString($label) . ",\n" ?>
        <?php endforeach; ?>
]);
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


   
}