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

class <?= $className ?> extends <?= '\\'.$generator->ns.'\\base\\'.$className.'Base' . "\n" ?>
{
    #region roles
    public function rules(){
        return array_merge(parent::rules(),[

        ]);
    }
    #endregion

    #region scopes
    public function scopes() {
        return [
        ];
    }
    #endregion

    #region attributeLabels
    public function attributeLabels(){
        return [
        <?php foreach ($labels as $name => $label): ?>
            <?= "'$name' => " . $generator->generateString($label) . ",\n" ?>
        <?php endforeach; ?>
        ];
    }
    #endregion


    public function search($pageSize = null, $defaultSort = '') {
        $criteria = new CDbCriteria;
        $sort = new CSort();
        $sort->defaultOrder = $defaultSort;
        <?php foreach($tableSchema->columns as $name=>$column): ?>
            <?php $partial = ($column->type==='string' and !$column->isPrimaryKey); ?>
            // $criteria->compare('<?php echo $name; ?>', $this-><?php echo $name; ?><?php echo $partial ? ', true' : ''; ?>);
        <?php endforeach; ?>

        return new CActiveDataProvider($this, [
            'criteria' => $criteria,
            'sort' => $sort,
            'pagination' => [
                'pageSize'=>isset($pageSize) ? $pageSize : param('default.pageSize'),
            ],
        ]);
    }
}