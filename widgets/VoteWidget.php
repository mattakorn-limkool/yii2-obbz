<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\widgets;


use hauntd\vote\widgets\Vote;

class VoteWidget extends Vote
{
    public function initJsEvents($selector)
    {
        if (!isset($this->jsBeforeVote)) {
            $this->jsBeforeVote = "
                $('$selector .vote-btn').prop('disabled', 'disabled').addClass('vote-loading');
                $('$selector .vote-count')
                    .addClass('vote-loading')
                    .append('<div class=\"vote-loader\"><span></span><span></span><span></span></div>');
            ";
        }
        if (!isset($this->jsAfterVote)) {
            $this->jsAfterVote = "
                $('$selector .vote-btn').prop('disabled', false).removeClass('vote-loading');
                $('$selector .vote-count').removeClass('vote-loading').find('.vote-loader').remove();
            ";
        }
        if (!isset($this->jsChangeCounters)) {
            $this->jsChangeCounters = "
                if (data.success) {
                    $('$selector .vote-count span').text(data.aggregate.positive - data.aggregate.negative);
                    vote.find('button').removeClass('vote-active');
                    button.addClass('vote-active');
                    $('$selector .vote-count-positive').text(data.aggregate.positive);
                    $('$selector .vote-count-negative').text(data.aggregate.negative);
                }
            ";
        }
    }
}