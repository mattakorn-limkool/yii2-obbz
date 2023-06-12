<?php

use yii\helpers\Html;
use obbz\yii2\themes\material\widgets\ActiveForm;
use obbz\yii2\utils\ObbzYii;

/**
 * @var $this yii\web\View
 * @var $model \obbz\yii2\admin\models\TranslateForm
 * @var $form obbz\yii2\themes\material\widgets\ActiveForm
 */



$updateUrl = \yii\helpers\Url::current(['key'=>$model->section, 'lang'=>'']);
?>
<?php $form = ActiveForm::begin(); ?>
	<div class="card">
		<div class="card-header ch-alt">
			<h2><?= Html::encode($this->title) ?></h2>
		</div>

		<div class="card-body card-padding">
			<div class="room-type-form row">

				<div class="col-md-12">

					<?php if(empty($model->messageModels)): ?>
						<div class="row">
							<div class="col-md-12">
								<div class="alert alert-danger">Translation not found</div>
							</div>
						</div>
					<?php else: ?>

						<div class="row">
							<div class="col-xs-5">
								<div class="alert alert-info">
									<p>From: <?php echo $model->fromLabel ?></p>
								</div>
							</div>
							<div class="col-xs-1 text-center">
								<div class="m-t-20"><i class="fa fa-chevron-right"></i></div>
							</div>
							<div class="col-xs-6">
								<?php echo $form->field($model, 'language')->dropDownList($model->listTranslationLanguages()) ?>
							</div>
							<div class="col-xs-12">
								<hr>
								<br>
							</div>
						</div>

						<?php foreach($model->messageModels as $model): ?>
							<div class="row">
								<div class="col-xs-5">
									<?php echo $form->field($model, '[]defaultMessage', ['inputOptions'=>['disabled'=>'disabled']])->textarea()->disableFloatingLabel() ?>
								</div>
								<div class="col-xs-1 text-center">
									<span><i class="fa fa-chevron-right"></i></span>
								</div>
								<div class="col-xs-6">
									<?php echo $form->field($model, '[]translateMessage')->textarea()->disableFloatingLabel()  ?>
								</div>
							</div>


						<?php endforeach; ?>

							<br>
							<div class="form-group row">
								<div class="col-md-offset-8 col-md-2 col-xs-6">
									<?php echo \obbz\yii2\widgets\ButtonLink::widget([
										'url'=>ObbzYii::referrerUrl(['index']),
										'text'=>\Yii::t('app', 'Back'),
										'prefixIcon'=>'chevron-circle-left',
										'btnClass'=>'default btn-block',
									]); ?>
								</div>
								<div class="col-md-2  col-xs-6">
									<?php echo \obbz\yii2\widgets\Button::widget([
										'text'=>\Yii::t('app', 'Save'),
										'btnClass'=>'primary  btn-block',
										'prefixIcon'=>'check'
									]) ?>
								</div>
							</div>
					<?php endif; ?>


				</div>




			</div>
		</div>

	</div>
<?php ActiveForm::end(); ?>

<?php

$this->registerJs( <<<JS

JS
	, \yii\web\View::POS_HEAD);


$this->registerJs( <<<JS
	$("#translateform-language").on("change", function(){
		location.href = '$updateUrl' + $(this).val();
	});
JS
); ?>