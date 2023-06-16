<?php

use yii\helpers\Html;
use obbz\yii2\themes\material\widgets\ActiveForm;
use obbz\yii2\utils\ObbzYii;

/**
 * @var $this yii\web\View
 * @var $model \obbz\yii2\admin\models\FlexibleModule
 * @var $form obbz\yii2\themes\material\widgets\ActiveForm
 */


//$updateUrl = \yii\helpers\Url::current(['key'=>$model->section, 'lang'=>'']);
//ObbzYii::debug($model->isNewRecord);
?>
	<style>
		.flexible-module-form .card{
			min-height: 469px;
		}
	</style>
<div class="flexible-module-form">
	<?php $form = ActiveForm::begin(['id'=>'flexible-module-form']); ?>
		<div class="card">
			<div class="card-body card-padding">
				<div class="row">
					<div class="col-xs-4">
						<?php echo $form->field($model, 'key_name')->dropDownList($model::getKeyList()) ?>
					</div>
					<div class="col-xs-4">
						<?php echo $form->field($model, 'column_pattern')->dropDownList($model->columnPatterns)?>
					</div>
					<div class="col-xs-4">
						<?php echo $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<h4>Upload Images</h4>
						<div class="row">
							<div class="col-md-12">
								<?php
								echo \obbz\yii2\widgets\fileupload\ImageUploadDbMultiple::widget([
									'model' => $model,
									'attribute' => 'uploadItems',
									'url' => ['image-upload'],
									'load' => $model->isNewRecord ? false: true,

//									'load' => true,
									//						'formView'=>'@frontend/components/widgets/views/product/multiple-upload/form',
									//						'downloadTemplateView' => '//image-upload/download',
									//						'uploadTemplateView' => '//image-upload/upload',
//												'clientEvents' => [
////													'fileuploadfail' => 'function(e, data) {
////									                                console.log(e);
////									                                console.log(data);
////									                            }',
//												],
								]);
								?>
							</div>
						</div>
					</div>
				</div>
			</div>

		</div>
	<?php ActiveForm::end(); ?>

</div>

<?php

$this->registerJs( <<<JS

JS
	, \yii\web\View::POS_HEAD);


$this->registerJs( <<<JS

JS
); ?>