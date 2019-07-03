<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\widgets;


use obbz\yii2\utils\ObbzYii;
use yii\base\Exception;
use yii\base\Widget;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

class CommentWidget extends Widget
{
    public $modelClass;
    public $modelId;
    public $action = ['comment-create'];
    public $query = null;
    public $orderBy = ['comment.created_time'=>SORT_DESC];
    public $pagination = ['defaultPageSize'=>5];
    public $messageSuccess = 'Comment has been saved.';
    public $withVote = true;
    public $voteQueryFunction = 'withVote';
    public $orderByRating = true;
    public $orderRagingEntity = 'commentVoteAggregate';
    public $viewFile = '@vendor/obbz/yii2/widgets/views/comment';
    public $viewFileItem = '@vendor/obbz/yii2/widgets/views/comment-item';

    public function init()
    {
        if($this->modelClass == null){
            throw new Exception('Please define $modelClass');
        }
        if($this->modelId == null){
            throw new Exception('Please define $modelId');
        }

        parent::init();
    }

    public function run()
    {

        return $this->render($this->viewFile, [
            'model' =>  $this->newModel(),
            'action' =>  $this->action + ['id'=>$this->modelId],
            'dataProvider'=>$this->getDataProvider(),
            'viewFileItem' => $this->viewFileItem,
            'withVote'=>$this->withVote
        ]);
//        return Html::encode($this->message);
    }


    /**
     * @return Comment
     */
    protected function newModel(){
        /** @var Comment $model */
        $model = new $this->modelClass;
        $model->setScenario($model::SCENARIO_CREATE);
        $model->model_id = $this->modelId;
        return $model;
    }


    protected function getDataProvider(){
        $query = $this->getQuery();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=>false,
            'pagination'=>$this->pagination
        ]);
        return $dataProvider;
    }

    protected function getQuery(){
        $model = $this->newModel();
        $modelClass = $this->modelClass;
        /** @var ActiveQuery $query */
        $query = isset($this->query) ?
            $this->query :
            $modelClass::find()->with(['createdUser'])->published()->andWhere(
                [
                    'model_id'=>$this->modelId,
                    'key_name'=>$model::getSectionKey()
                ]);

        if($this->withVote){
            $func = $this->voteQueryFunction;
            $query->$func();
            if($this->orderByRating){
                $this->orderBy = [new Expression('IFNULL(`' . $this->orderRagingEntity . '`.`rating`,0) DESC')]
                    + $this->orderBy;
            }
        }

        $this->additionalQuery($query, $model, $modelClass);

        return $query->orderBy($this->orderBy);
    }

    /**
     * @param $query ActiveQuery
     * @param $model ActiveRecord
     * @param $modelClass string
     */
    protected function additionalQuery(&$query, $model, $modelClass){}

}