<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\models;


class Vote extends \hauntd\vote\models\Vote
{
    /**
     * @param $entity
     * @param $targetId
     */
    public static function updateRating($entity, $targetId)
    {
        $positive = static::find()->where(['entity' => $entity, 'target_id' => $targetId, 'value' => self::VOTE_POSITIVE])->count();
        $negative = static::find()->where(['entity' => $entity, 'target_id' => $targetId, 'value' => self::VOTE_NEGATIVE])->count();
//        if ($positive + $negative !== 0) {
//            $rating = (($positive + 1.9208) / ($positive + $negative) - 1.96 * SQRT(($positive * $negative)
//                        / ($positive + $negative) + 0.9604) / ($positive + $negative)) / (1 + 3.8416 / ($positive + $negative));
//        } else {
//            $rating = 0;
//        }
        $rating = $positive - $negative;
//        $rating = round($rating * 10, 2);
        $aggregateModel = VoteAggregate::findOne([
            'entity' => $entity,
            'target_id' => $targetId,
        ]);
        if ($aggregateModel == null) {
            $aggregateModel = new VoteAggregate();
            $aggregateModel->entity = $entity;
            $aggregateModel->target_id = $targetId;
        }
        $aggregateModel->positive = $positive;
        $aggregateModel->negative = $negative;
        $aggregateModel->rating = $rating;
        $aggregateModel->save();
    }
}