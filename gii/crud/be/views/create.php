<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

echo "<?php\n";
?>

use yii\helpers\Html;
use obbz\yii2\utils\ObbzYii;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */

$this->title = <?= $generator->generateString('Create ' . Inflector::camel2words(StringHelper::basename($generator->modelClass))) ?>;
$this->context->showTitle = false;
$this->params['breadcrumbs'][] = ['label' => <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card <?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-create">
	<div class="card-header ch-alt">
        <h2><?= "<?= " ?>Html::encode($this->title) ?></h2>
    </div>

	<div class="card-body card-padding">
		<?= "<?= " ?>$this->render('_form', [
			'model' => $model,
		]) ?>
	</div>

</div>
