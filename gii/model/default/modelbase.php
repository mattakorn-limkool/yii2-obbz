<?php
/**
 * This is the template for generating the model class of a specified table.
 */

/* @var $this yii\web\View */
/* @var $generator obbz\yii2\gii\model\Generator */
/* @var $tableName string full table name */
/* @var $className string class name */
/* @var $queryClassName string query class name */
/* @var $tableSchema yii\db\TableSchema */
/* @var $labels string[] list of attribute labels (name => label) */
/* @var $rules string[] list of validation rules */
/* @var $relations array list of relations (name => relation declaration) */

echo "<?php\n";

$searchConditions = $generator->generateSearchConditions();
?>

namespace <?= $generator->ns ?>\base;

use Yii;

<?php
$hasDone = [];
foreach ($relations as $name => $relation) {
    if (!in_array($relation[1], $hasDone)) {
        echo "use $generator->ns\\$relation[1];\n";
    }
    $hasDone[] = $relation[1];
}
?>

/**
* This is the model class for table "<?= $generator->generateTableName($tableName) ?>".
* DO NOT MODIFY THIS FILE!
* If any changes are necessary, you must set or override the required property or method in class
*
<?php foreach ($tableSchema->columns as $column): ?>
    * @property <?= "{$column->phpType} \${$column->name}\n" ?>
<?php endforeach; ?>
<?php if (!empty($relations)): ?>
    *
    <?php foreach ($relations as $name => $relation): ?>
        * @property <?= $relation[1] . ($relation[2] ? '[]' : '') . ' $' . lcfirst($name) . "\n" ?>
    <?php endforeach; ?>
<?php endif; ?>
*/
class <?= $className ?>Base extends <?= '\\' . ltrim($generator->baseClass, '\\') . "\n" ?>
{

    /**
    * @inheritdoc
    */
    public static function tableName()
    {
        return '<?= $generator->generateTableName($tableName) ?>';
    }
<?php if ($generator->db !== 'db'): ?>

    /**
    * @return \yii\db\Connection the database connection used by this AR class.
    */
    public static function getDb()
    {
        return Yii::$app->get('<?= $generator->db ?>');
    }
<?php endif; ?>


    public function rules()
    {
        return array_merge(parent::rules(),[<?= "\n            " . implode(",\n            ", $rules) . ",\n        " ?>]);
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
<?php if ($queryClassName): ?>
<?php
    $queryClassFullName = ($generator->ns . '\base' === $generator->queryNs) ? $queryClassName : '\\' . $generator->queryNs . '\\' . $queryClassName;
    echo "\n";
?>
    /**
     * @inheritdoc
     * @return <?= $queryClassFullName ?> the active query used by this AR class.
     */
    public static function find()
    {
        return new <?= $queryClassFullName ?>(get_called_class());
    }
<?php endif; ?>
	
	/**
     * @param $query \yii\db\ActiveQuery
     */
    public function defaultQueryFilter(&$query){
		// grid filtering conditions
        $this->prepareCoreAttributesFilter();

        <?= implode("\n        ", $searchConditions) ?>
	}
}