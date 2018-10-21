<?php

use hauntd\vote\models\Vote;

/* @var $jsCodeKey string */
/* @var $entity string */
/* @var $model \yii\db\ActiveRecord */
/* @var $targetId integer */
/* @var $userValue null|integer */
/* @var $positive integer */
/* @var $negative integer */
/* @var $rating float */
/* @var $options array */

?>
<div class="<?= $options['class'] ?>"
     data-rel="<?= $jsCodeKey ?>"
     data-entity="<?= $entity ?>"
     data-target-id="<?= $targetId ?>"
     data-user-value="<?= $userValue ?>">
    <div class="vote-count hidden">
        <span><?= $positive - $negative ?></span>
    </div>


    <button class="vote-btn vote-up <?= $userValue === Vote::VOTE_POSITIVE ? 'vote-active' : '' ?>" data-action="positive">
        <i class="fa fa-thumbs-up"></i>

    </button>
    <span class="vote-count-positive"> <?= $positive ?></span>

    <button class="vote-btn vote-down <?= $userValue === Vote::VOTE_NEGATIVE ? 'vote-active' : '' ?>" data-action="negative">
        <i class="fa fa-thumbs-down"></i>
    </button>
    <span class="vote-count-negative"> <?= $negative ?></span>


</div>
