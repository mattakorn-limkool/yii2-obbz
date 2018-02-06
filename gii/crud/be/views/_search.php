<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

echo "<?php\n";
?>

use yii\helpers\Html;
use obbz\yii2\themes\material\widgets\ActiveForm;
use obbz\yii2\utils\ObbzYii;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->searchModelClass, '\\') ?> */
/* @var $form obbz\yii2\themes\material\widgets\ActiveForm */
?>
<div id="core-filter" class="row core-filter collapse">
	<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-search">

    <?= "<?php " ?>$form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
<?php if ($generator->enablePjax): ?>
        'options' => [
            'data-pjax' => 1
        ],
		'layout' => 'inline'
<?php endif; ?>
    ]); ?>

<?php
$count = 0;
$showAttributes = ['title', 'detail'];
$noFilterAttributes = ['image', 'sorting', 'modify_user_id', 'deleted_user_id'];
/** not implement other field yet (created_time, modify_time, deleted_time, create_user_id)   */
foreach ($generator->getColumnNames() as $attribute) {
	
    if (in_array($attribute, $showAttributes)) {
		echo "	<div class=\"col-sm-3\">\n";
        echo "    <?php echo " . $generator->generateActiveSearchField($attribute) . " ?>\n";
		echo "	</div>\n\n";
    }
    else if(in_array($attribute, $noFilterAttributes)){} // by pass
    else {
		echo "	<!--<div class=\"col-sm-3\">\n";
        echo "    <?php /* echo " . $generator->generateActiveSearchField($attribute) . " */?>\n";
		echo "	</div>-->\n\n";
    }
	
}
?>
		<div class="col-sm-2">
			<?= "<?php echo " ?>Html::submitButton('<i class="fa fa-search"></i> ' . <?= $generator->generateString('Search') ?>, ['class' => 'btn btn-primary btn-block']) ?>
			<?= "<?php // echo " ?>Html::resetButton(<?= $generator->generateString('Reset') ?>, ['class' => 'btn btn-default']) ?>
		</div>

    <?= "<?php " ?>ActiveForm::end(); ?>

	</div>
</div>
