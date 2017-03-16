<?php


namespace obbz\yii2\gii\normalmodel;

use Yii;
use yii\gii\CodeFile;
use yii\db\Schema;
use yii\helpers\VarDumper;

/**
 * This generator will generate one or multiple ActiveRecord classes for the specified database table.
 *
 * @author Mattakorn Limkool
 * @since 2.0
 */
class Generator extends \yii\gii\generators\model\Generator
{
    public $ns = 'common\models';
    public $queryNs = 'common\models\query';
//    public $queryBaseClass = 'obbz\yii2\models\CoreActiveQuery';
//    public $baseClass = 'obbz\yii2\models\CoreActiveRecord';

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Obbz Normal Model Generator';
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return 'This generator generates two ActiveRecord class for the specified database table. An empty one you can extend and a Base one which is the same as the original model generator.';
    }

    /**
     * @inheritdoc
     */
    public function requiredTemplates()
    {
        return ['model.php', 'modelbase.php'];
    }

    public function stickyAttributes()
    {
        return array_merge(parent::stickyAttributes(), ['generateQuery']);
    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        $files = [];
        $relations = $this->generateRelations();
        $db = $this->getDbConnection();
        foreach ($this->getTableNames() as $tableName) {
            // model :
            $modelClassName = $this->generateClassName($tableName);
            $queryClassName = ($this->generateQuery) ? $this->generateQueryClassName($modelClassName) : false;
            $tableSchema = $db->getTableSchema($tableName);
            $params = [
                'tableName' => $tableName,
                'className' => $modelClassName,
                'queryClassName' => $queryClassName,
                'tableSchema' => $tableSchema,
                'labels' => $this->generateLabels($tableSchema),
                'rules' => $this->generateRules($tableSchema),
                'relations' => isset($relations[$tableName]) ? $relations[$tableName] : [],
            ];
            $files[] = new CodeFile(
                Yii::getAlias('@' . str_replace('\\', '/', $this->ns)) . '/' . $modelClassName . '.php', $this->render('model.php', $params)
            );
            $files[] = new CodeFile(
                Yii::getAlias('@' . str_replace('\\', '/', $this->ns)) . '/base/' . $modelClassName . 'Base.php', $this->render('modelbase.php', $params)
            );
            // query :
            if ($queryClassName) {
                $params['className'] = $queryClassName;
                $params['modelClassName'] = $modelClassName;
                $files[] = new CodeFile(
                    Yii::getAlias('@' . str_replace('\\', '/', $this->queryNs)) . '/' . $queryClassName . '.php', $this->render('query.php', $params)
                );
            }
        }
        return $files;
    }



    /**
     * Generates search conditions
     * @return array
     */
    public function generateSearchConditions()
    {
        $db = $this->getDbConnection();
        $columns = [];
        if (($table = $db->getTableSchema($this->tableName)) === false) {
            $class = $this->modelClass;
            /* @var $model \yii\base\Model */
            $model = new $class();
            foreach ($model->attributes() as $attribute) {
                $columns[$attribute] = 'unknown';
            }
        } else {
            foreach ($table->columns as $column) {
                $columns[$column->name] = $column->type;
            }
        }

        $likeConditions = [];
        $hashConditions = [];
        foreach ($columns as $column => $type) {

            // specific for core table
            if($column === 'key_name'){
                $hashConditions[] = "'{$column}' => \$this->{$column},";
            }else{
                switch ($type) {
                    case Schema::TYPE_SMALLINT:
                    case Schema::TYPE_INTEGER:
                    case Schema::TYPE_BIGINT:
                    case Schema::TYPE_BOOLEAN:
                    case Schema::TYPE_FLOAT:
                    case Schema::TYPE_DOUBLE:
                    case Schema::TYPE_DECIMAL:
                    case Schema::TYPE_MONEY:
                    case Schema::TYPE_DATE:
                    case Schema::TYPE_TIME:
                    case Schema::TYPE_DATETIME:
                    case Schema::TYPE_TIMESTAMP:
                        $hashConditions[] = "'{$column}' => \$this->{$column},";
                        break;
                    default:

                        if ($this->getDbDriverName() === 'pgsql') {
                            $likeConditions[] = "->andFilterWhere(['ilike', '{$column}', \$this->{$column}])";
                        } else {
                            $likeConditions[] = "->andFilterWhere(['like', '{$column}', \$this->{$column}])";
                        }
                        break;
                }
            }

        }

        $conditions = [];
        if (!empty($hashConditions)) {
            $conditions[] = "\$query->andFilterWhere([\n"
                . str_repeat(' ', 12) . implode("\n" . str_repeat(' ', 12), $hashConditions)
                . "\n" . str_repeat(' ', 8) . "]);\n";
        }
        if (!empty($likeConditions)) {
            $conditions[] = "\$query" . implode("\n" . str_repeat(' ', 12), $likeConditions) . ";\n";
        }

        return $conditions;
    }

    public function generateString($string = '', $placeholders = [])
    {
        $string = addslashes($string);
        if ($this->enableI18N) {
            // If there are placeholders, use them
            if (!empty($placeholders)) {
                $ph = ', ' . VarDumper::export($placeholders);
            } else {
                $ph = '';
            }
            $messageCategory = empty($this->messageCategory) ? 'model/' . $this->tableName2name($this->tableName) : $this->messageCategory;
            $str = "\\Yii::t('". $messageCategory ."', '" . $string . "'" . $ph . ")";

        } else {
            // No I18N, replace placeholders by real words, if any
            if (!empty($placeholders)) {
                $phKeys = array_map(function($word) {
                    return '{' . $word . '}';
                }, array_keys($placeholders));
                $phValues = array_values($placeholders);
                $str = "'" . str_replace($phKeys, $phValues, $string) . "'";
            } else {
                // No placeholders, just the given string
                $str = "'" . $string . "'";
            }
        }
        return $str;
    }

    public function tableName2name($tableName){
        return str_replace('_', '-', $tableName);
    }

    public function validateMessageCategory()
    {
        return true;
    }
}
