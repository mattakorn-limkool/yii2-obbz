<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();

echo "<?php\n";
?>

use yii\helpers\Html;
use yii\widgets\DetailView;
use obbz\yii2\utils\ObbzYii;
use obbz\yii2\widgets\ButtonLink;

/**
 * @var $this yii\web\View
 * @var $model <?= ltrim($generator->modelClass, '\\') ?>
 */


$this->title = $model-><?= $generator->getNameAttribute() ?>;
$this->context->showTitle = false;
//$this->params['breadcrumbs'][] = ['label' => <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>, 'url' => ['index']];
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-view card">
	 <div class="card-header">
        <h2><?= "<?= " ?>Html::encode($this->title) ?></h2>
        <ul class="actions ">
            <li>
			<?= "<?php echo " ?>ButtonLink::widget([
				"url"=>['update', 'id' => $model->id],
				'text'=>\Yii::t('app', 'Update'),
				"btnClass"=>"primary",
				'prefixIcon'=>'edit'
			]); ?>
            </li>

        </ul>
    </div>
	<div class="card-body">
    <?= "	<?php echo " ?>DetailView::widget([
			'model' => $model,
			'attributes' => [
<?php
if (($tableSchema = $generator->getTableSchema()) === false) {
    foreach ($generator->getColumnNames() as $name) {
        echo "            	'" . $name . "',\n";
    }
} else {
    foreach ($generator->getTableSchema()->columns as $column) {
        $format = $generator->generateColumnFormat($column);
        echo "            	'" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
    }
}
?>
			],
		]) ?>
	  </div>
</div>
