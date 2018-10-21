<?php

/* @var $this yii\web\View */
/**
 * @var \common\models\Comment $model
 */

use yii\helpers\Html;
use frontend\widgets\FeActiveForm;
use yii\widgets\Pjax;

?>
<?php //Pjax::begin(['id'=>"comment-pjax-area"]); ?>
<div class="wall-comment-list">
    <div class="wcl-form">
        <div class="wc-comment">
            <h4>Comment</h4>
            <?php
            /** @var FeActiveForm $form */
            $form = FeActiveForm::begin([
                'action'=>$action,
                'options'=>['data-pjax'=>1]
            ]); ?>

            <?php echo $form->field($model, 'content')->textarea(['rows'=>3])->label(false); ?>
            <div class="text-right">
                <button class="btn btn-primary">Comment</button>
            </div>

            <?php $form->end(); ?>
        </div>
    </div>

    <br>
<!--    <div class="wcl-list">-->

        <?php echo \frontend\widgets\FeListInfScrollView::widget([
            'id' => 'reviews',
            'dataProvider'=>$dataProvider,
            'itemView'=>$viewFileItem,
            'emptyText' => false,
            'viewParams'=>[
                'withVote'=>$withVote
            ],
//            'pagerAdditional' => [
//                'container' => '#reviews.list-view'
//            ]
        ]); ?>
<!--    </div>-->



</div>
<?php //Pjax::end(); ?>
