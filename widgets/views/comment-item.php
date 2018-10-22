<?php

/* @var $this yii\web\View */
/**
 * @var \common\models\Comment $model
 */

use yii\helpers\Html;
use obbz\yii2\utils\ObbzYii;
use frontend\widgets\FeActiveForm;

?>
<div class="media">
    <div  class="pull-left">
        <img src="<?php echo $model->createdUser->getThumbUploadUrl('img');  ?>" alt="" class="lv-img-sm">
    </div>

    <div class="media-body">
        <span class="text-primary"><?php echo $model->createdUser->display_name  ?></span>
        <small class="c-gray m-l-10"><?php echo ObbzYii::formatter()->asTimeAgo($model->created_time) ?></small>
        <p class="m-t-5 m-b-10"><?php echo ObbzYii::formatter()->asNtext($model->content) ?></p>
        <?php if($withVote): ?>
        <div class="comment-vote-tool">
            <?php echo \obbz\vote\widgets\Vote::widget([
                'entity' => $model::PLUGIN_VOTE,
                'model' => $model,
                'options' => ['class' => 'vote vote-visible-buttons'],
                ''
            ]); ?>
        </div>
        <?php endif; ?>
    </div>
    <hr>
</div>

