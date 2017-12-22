<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

/* @var $model \yii\db\ActiveRecord */
$model = new $generator->modelClass();
$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
    $safeAttributes = $model->attributes();
}
// unset image
if(($key = array_search('image', $safeAttributes)) !== false) {
    unset($safeAttributes[$key]);
}


$coreAttributes = method_exists($model, 'getCoreAttributes') ?  $model->getCoreAttributes() : [];
$commentAttributes = $coreAttributes;
// unset title
if(($key = array_search('title', $commentAttributes)) !== false) {
    unset($commentAttributes[$key]);
}
// unset detail
if(($key = array_search('detail', $commentAttributes)) !== false) {
    unset($commentAttributes[$key]);
}

echo "<?php\n";
?>

use yii\helpers\Html;
use obbz\yii2\themes\material\widgets\ActiveForm;
use obbz\yii2\utils\ObbzYii;

/**
 * @var $this yii\web\View
 * @var $model <?= ltrim($generator->modelClass, '\\') ?>
 * @var $form obbz\yii2\themes\material\widgets\ActiveForm
 */

$this->context->showTitle = true;
$this->context->headerActions = [
	Html::a('<i class="fa fa-trash"></i>', ['delete', 'id'=>$model->id],
		[
			'data' => [
				'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
				'method' => 'post',
			]
		]
	)
];
?>
<?= "<?php " ?>$form = ActiveForm::begin(); ?>
	<div class="card">
		<div class="card-header ch-alt">
			<h2><?= "<?= " ?>Html::encode($this->title) ?></h2>
		</div>

		<div class="card-body card-padding">
			<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-form row">

				<div class="col-md-4">
					<?php echo "    	<?php echo " . $generator->generateActiveField('image') . " ?>\n"; ?>
				</div>
				<div class="col-md-8">

<?php foreach ($generator->getColumnNames() as $attribute) {
	if (in_array($attribute, $safeAttributes)) {
		if(in_array($attribute, $commentAttributes)){
			echo "    				<?php //echo " . $generator->generateActiveField($attribute) . " ?>\n\n";
		}
		else{
			echo "    				<?php echo " . $generator->generateActiveField($attribute) . " ?>\n\n";
		}

	}
} ?>


				</div>
				<div class="form-group row">
					<div class="col-md-offset-8 col-md-2 col-xs-6">
						<?= "<?php echo " ?>\obbz\yii2\widgets\ButtonLink::widget([
						'url'=>ObbzYii::referrerUrl(['index']),
						'text'=>\Yii::t('app', 'Back'),
						'prefixIcon'=>'chevron-circle-left',
						'btnClass'=>'default btn-block',
						]); ?>
					</div>
					<div class="col-md-2  col-xs-6">
						<?= "<?php echo " ?>\obbz\yii2\widgets\Button::widget([
						'text'=><?= $generator->generateString('Save') ?>,
						'btnClass'=>'primary  btn-block',
						'prefixIcon'=>'check'
						]) ?>
					</div>
				</div>




			</div>
		</div>

	</div>
<?= "<?php " ?>ActiveForm::end(); ?>

