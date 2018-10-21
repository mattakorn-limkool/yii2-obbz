<?php
use yii\helpers\Html;
use obbz\yii2\themes\material\widgets\ActiveForm;
use obbz\yii2\utils\ObbzYii;
use obbz\yii2\actions\CoreTranslate;
/**
 * @author: Mattakorn Limkool
 * @var $model \obbz\yii2\models\CoreActiveRecord
 * @var $translateModel \obbz\yii2\models\CoreActiveRecord
 * @var $translationAttributes
 * @var $attributesOptions
 */
$languageLabel = isset(\Yii::$app->params['languages'][$language]) ? \Yii::$app->params['languages'][$language]: $language;
$this->title =   \Yii::t('obbz', 'Translate to ') . $languageLabel;
?>

<div class="card branch-update">
    <div class="card-header ch-alt">
        <h2><?php echo Html::encode($this->title) ?></h2>
    </div>
    <?php $form = ActiveForm::begin(); ?>
    <div class="card-body card-padding translate-form ">
        <?php if(!empty($message)): ?>
            <div class="alert alert-<?php echo $hasError? 'danger': 'success' ?>"><?php echo $message; ?></div>
        <?php endif; ?>
        <div class="row">
<!--            <div class="col-md-6">-->
<!--                --><?php //echo $form->field($model, $attribute)->textarea(['rows' => 3, 'disabled'=>'disabled']) ?>
<!--            </div>-->
            <div class="col-md-12">
                <?php foreach($translationAttributes as $attribute): ?>
                    <?php
                    $options = isset($attributesOptions[$attribute]['options']) ? $attributesOptions[$attribute]['options'] : [];
                    switch($attributesOptions[$attribute]['type']){
                        case CoreTranslate::INPUT_TYPE_TEXT :
                            echo $form->field($translateModel, "[$language]".$attribute)->textInput($options);
                            break;
                        case CoreTranslate::INPUT_TYPE_RTE :
                            echo $form->field($translateModel, "[$language]".$attribute)->rte($options);
                            break;
                        default:
                            echo $form->field($translateModel, "[$language]".$attribute)->textarea($options);
                            break;
                    }

                    ?>
                <?php endforeach; ?>
            </div>
            <div class="form-group col-md-12">

                <?php echo \obbz\yii2\widgets\Button::widget([
                    'text'=>\Yii::t('obbz', 'Save Translate'),
                    'btnClass'=>'primary',
                    'prefixIcon'=>'save'
                ]) ?>

<!--                --><?php //echo \obbz\yii2\widgets\ButtonLink::widget([
//                    'url'=>ObbzYii::referrerUrl(['index']),
//                    'text'=>\Yii::t('app', 'Cancel'),
////                'prefixIcon'=>'chevron-left'
//                ]); ?>

            </div>
        </div>



    </div>


    <?php ActiveForm::end(); ?>
</div>

