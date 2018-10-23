<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\behaviors;


use obbz\yii2\models\Rating;
use obbz\yii2\models\RatingAggregate;
use yii\base\Behavior;
use yii\db\Expression;

class RatingQueryBehavior extends Behavior
{
    /**
     * @var bool
     */
    protected $selectAdded = false;


    /**
     * Include all default join and condition for vote
     * @param $entity
     * @param $version default 1
     * @return \yii\db\ActiveQuery
     */
    public function withRatingPlugin($entity, $version=1){
        $this->owner
            ->withRateAggregate($entity, $version)
            ->withUserRate($entity, $version)
        ;
        return $this->owner;
    }

    /**
     * Include vote aggregate model/values.
     *
     * @param $entity
     * @param $version default 1
     * @return \yii\base\Component
     * @throws \yii\base\InvalidConfigException
     */
    public function withRateAggregate($entity, $version = 1)
    {

        $table = RatingAggregate::tableName();
        $model = new $this->owner->modelClass();
        $this->initSelect($model);

        $this->owner
            ->leftJoin("$table {$entity}Aggregate", [
                "{$entity}Aggregate.target_id" => new Expression("`{$model->tableSchema->name}`.`{$model->primaryKey()[0]}`"),
                "{$entity}Aggregate.entity" => $entity,
                "{$entity}Aggregate.version" => $version,
            ])
            ->addSelect([
                new Expression("`{$entity}Aggregate`.`version` as `{$entity}Version`"),
                new Expression("`{$entity}Aggregate`.`value` as `{$entity}Value`"),
                new Expression("`{$entity}Aggregate`.`amount` as `{$entity}Amount`"),
                new Expression("`{$entity}Aggregate`.`rating` as `{$entity}Rating`"),
            ]);

        return $this->owner;
    }

    public function withUserRate($entity, $version = 1)
    {
        $model = new $this->owner->modelClass();
        $rateTable = Rating::tableName();
        $this->initSelect($model);

        $joinCondition = [
            "$entity.entity" => $entity,
            "$entity.version" => $version,
            "$entity.target_id" => new Expression("`{$model->tableSchema->name}`.`{$model->primaryKey()[0]}`"),
        ];

        $this->owner->addGroupBy("`{$model->tableSchema->name}`.`{$model->tableSchema->primaryKey[0]}`");
//        if (Yii::$app->user->isGuest) {
//            $joinCondition["{$entity}.user_ip"] = Yii::$app->request->userIP;
//            $joinCondition["{$entity}.user_id"] = null;
//        } else {
        $joinCondition["{$entity}.user_id"] = \Yii::$app->user->id;
//        }

        $this->owner
            ->leftJoin("$rateTable $entity", $joinCondition)
            ->addSelect([
                new Expression("`$entity`.`value` as `{$entity}UserValue`")]);

        return $this->owner;
    }
    /**
     * Add `{{%table}}`.* as first table attributes to select.
     *
     * @param $model
     */
    protected function initSelect($model)
    {
        if (!$this->selectAdded && (is_array($this->owner->select) && !array_search('*', $this->owner->select)) ||
            !isset($this->owner->select)) {
            $this->owner->addSelect("{$model->tableSchema->name}.*");
            $this->selectAdded = true;
        }
    }
}