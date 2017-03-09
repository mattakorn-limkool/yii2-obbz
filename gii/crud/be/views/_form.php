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
?>

<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-form row">
	<?= "<?php " ?>$form = ActiveForm::begin(); ?>
	<div class="col-md-4">
<?php echo "    	<?php echo " . $generator->generateActiveField('image') . " ?>\n"; ?>
	</div>
	<div class="col-md-8">
    
<?php foreach ($generator->getColumnNames() as $attribute) {
    if (in_array($attribute, $safeAttributes)) {
		if(in_array($attribute, $commentAttributes)){
			echo "    	<?php //echo " . $generator->generateActiveField($attribute) . " ?>\n\n";
		}
		else{
			echo "    	<?php echo " . $generator->generateActiveField($attribute) . " ?>\n\n";
		}
        
    }
} ?>
		
		
	</div>
	<div class="form-group col-md-12 text-right">
		<?= "<?php echo " ?>\obbz\yii2\widgets\ButtonLink::widget([
				'url'=>ObbzYii::referrerUrl(['index']),
				'text'=>ObbzYii::t('Back'),
				'prefixIcon'=>'chevron-left'
			]); ?>
			<?= "<?php echo " ?>\obbz\yii2\widgets\Button::widget([
				'text'=><?= $generator->generateString('Save') ?>,
				'btnClass'=>'primary',
				'prefixIcon'=>'save'
			]) ?>
			
	</div>
	

    <?= "<?php " ?>ActiveForm::end(); ?>

</div>
