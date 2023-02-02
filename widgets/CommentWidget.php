<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\widgets;


use common\models\Comment;
use obbz\yii2\utils\ObbzYii;
use yii\base\Exception;
use yii\base\Widget;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * require comment attribute
 *
 * Class CommentWidget
 * @package obbz\yii2\widgets
 */
class CommentWidget extends Widget
{
    public $modelClass;
    public $modelId;
    public $modelField = 'model_id';
    public $action = ['comment-create'];
    public $query = null;
    public $orderBy = ['comment.id'=>SORT_DESC];
    public $pagination = ['defaultPageSize'=>5];
    public $messageSuccess = 'Comment has been saved.';
    public $withVote = true;
    public $voteQueryFunction = 'withVote';
    public $orderByRating = true;
    public $orderRagingEntity = 'commentVoteAggregate';
    public $viewFile = '@vendor/obbz/yii2/widgets/views/comment';
    public $viewFileItem = '@vendor/obbz/yii2/widgets/views/comment-item';

    /**
     * enable reply mode
     * @var bool
     */
    public $withReply = true;
    public $replyOptions = [
        'parentField'=>'parent_id',
        'showInputOnClickReplyBtn' => true, // set false on show replay input always.
//        'actionCreate' => ['/blog/comment-reply', 'action'=>'create'],
        'pagination' => false,
        'orderBy' => ['comment.id'=>SORT_ASC],
//        'actionCreate' => ['/blog/view', 'action'=>'create-reply'],
//        'actionList' => ['/blog/comment-reply', 'action'=>'list'],
    ];

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
        $id = $this->getId();
        $this->registerClientScript($id);


        if($this->withReply){
            $post = ObbzYii::post();
            $parentField = ArrayHelper::getValue($this->replyOptions, 'parentField');
            // action
            if(ArrayHelper::getValue($post, 'action') == 'create-reply'){

                $parentModel = $this->newModel();
                /** @var Comment $replyModel */
                $replyModel = $parentModel::newReplyModel();
                $replyModel->load($post);
                $replyModel->model_id = $this->modelId;
                $replyModel->ip_address = ObbzYii::getIpAddress();
                $replyModel->key_name = $parentModel::getSectionKey();
                if($replyModel->save()){
                    if(!ObbzYii::isAjax()){
                        ObbzYii::setFlashSuccess('Your comment has been added.');
                    }
                }else{
                    if(!ObbzYii::isAjax()){
                        ObbzYii::setFlashError($replyModel->getFirstErrors());
                    }
                }
            }

        }



        return $this->render($this->viewFile, [
            'model' =>  $this->newModel(),
            'action' =>  $this->action + ['id'=>$this->modelId],
            'dataProvider'=>$this->getDataProvider(),
            'viewFileItem' => $this->viewFileItem,
            'withVote'=>$this->withVote,
            'withReply'=>$this->withReply,
            'replyOptions'=>$this->replyOptions,
        ]);
//        return Html::encode($this->message);
    }


    /**
     * @return Comment
     */
    public function newModel(){
        /** @var Comment $model */
        $modelClass = $this->modelClass;
        $model = $modelClass::newModel($this->modelId);
        return $model;
    }

//    public function newReplyModel($parentId, $moreAttribute = []){
//        $parentField = ArrayHelper::getValue($this->replyOptions, 'parentField');
//        /** @var Comment $model */
//        $model = new $this->modelClass;
//        $model->setScenario($model::SCENARIO_REPLY_CREATE);
//        $model->$parentField = $parentId;
//        return $model;
//    }
//

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
                    $this->modelField=>$this->modelId,
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

        if($this->withReply){
            $parentField = ArrayHelper::getValue($this->replyOptions, 'parentField');
            $query->andWhere([$parentField => null]);
        }

        $this->additionalQuery($query, $model, $modelClass);

        return $query->orderBy($this->orderBy);
    }

    public function getReplyDataProvider($parent_id){
        $query = $this->getReplyQuery($parent_id);
        $pagination = ArrayHelper::getValue($this->replyOptions, 'pagination');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=>false,
            'pagination'=>$pagination
//            'pagination'=>false
        ]);
        return $dataProvider;
    }

    public function getReplyQuery($parent_id){
        $model = $this->newModel();
        $modelClass = $this->modelClass;
        $parentField = ArrayHelper::getValue($this->replyOptions, 'parentField');
        /** @var ActiveQuery $query */
        $query = isset($this->query) ?
            $this->query :
            $modelClass::find()->with(['createdUser'])->published()->andWhere(
                [
                    $parentField=>$parent_id,
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
        $orderBy = ArrayHelper::getValue($this->replyOptions, 'orderBy');
        return $query->orderBy($orderBy);
    }

    /**
     * @param $query ActiveQuery
     * @param $model ActiveRecord
     * @param $modelClass string
     */
    protected function additionalQuery(&$query, $model, $modelClass){}


    public function registerClientScript($id)
    {

        if($this->withReply){
            $css = '
                .comment-reply-link{cursor: pointer;}
                .reply-items {}
            ';

            $widgetName = 'commentWidget' . $id;
            $scriptHead = '
                var '. $widgetName .' = {
                    init: function(){
                        if('. Json::encode(ArrayHelper::getValue($this->replyOptions, 'showInputOnClickReplyBtn')) .'){
                            $("[comment-reply-input-area]").hide();

                            $("[comment-reply-btn]").on("click",function(){
                                var parentId = $(this).data("parent-id");
                                $("[comment-reply=\""+ parentId +"\"] [comment-reply-input-area]").show();
                            })
                        }else{
                            $("[comment-reply-btn]").hide();
                        }

                    }
                }
            ';

            $script = '
               '. $widgetName .'.init();
               $(document).on("ready pjax:success", function() {
                    '. $widgetName .'.init();
                });
			';

            $view = $this->getView();
            $view->registerCss($css);

            $view->registerJs($scriptHead, $view::POS_HEAD);
            $view->registerJs($script);

        }

    }

    public function callClientScriptInit(){
        $id = $this->getId();
        $widgetName = 'commentWidget' . $id;
        return 'function(){' . $widgetName . '.init();}';
    }


}