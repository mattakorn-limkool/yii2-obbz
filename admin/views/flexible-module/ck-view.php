<?php
/**
 * @author  Mattakorn Limkool <obbz.dev@gmail.com>
 * @var $model \obbz\yii2\admin\models\FlexibleModule
 */
use obbz\yii2\utils\ArrayHelper;
//echo \yii\helpers\Url::to(['ck-view', 'id'=>$model->id], true);
$title = ArrayHelper::getValue($model::getKeyList(), $model->key_name, '') .
    ' : ' . ArrayHelper::getValue($model->columnPatterns, $model->column_pattern, '');

if($this->title){
    $title .= ' : ' . $this->title;
}


?>
<style>
    .flexible-module-iframe{
        border: solid 1px #ccc;
        background: #f7f7f7;
        text-align: center;
        cursor: pointer;
        padding: 10px;
        width: 898px;
        height: 265px;
        box-shadow: 7px 7px 15px #dddada;
        overflow: hidden;
    }
    .flexible-module-iframe .title{
        font-size: 15px;
        font-weight: bold;
        padding: 0 0 5px;
    }
    .flexible-module-iframe .detail{
        font-size: 14px;
    }
    .flexible-module-iframe .thumb {
        margin: 20px 0;
    }
    .flexible-module-iframe .thumb img{
        object-fit: cover;
        width: 200px;
        height: 200px;
    }
    .flexible-module-iframe .not-found{
        margin-top: 50px;
    }
    .flexible-module-iframe .not-found .alert {
        padding: 30px;
        font-size: 20px;
    }
    .flexible-module-iframe .not-found .alert-warning {
        background-color: #fcf8e3;
        border-color: #faebcc;
        color: #333;
    }
</style>

<div class="flexible-module-iframe">
    <div class="row">
        <div class="col-xs-12">
            <span class="title"><?php echo $title; ?> </span>
            <button id="edit" type="button" class="btn btn-primary btn-xs">Edit</button>
        </div>

    </div>
    <div class="row">
            <?php
            $items = $model->getRelateItems(4);
            if($items):
            ?>

                <?php foreach($items as $item): ?>
                <div class="col-xs-3">
                        <div class="thumb">
                            <img src="<?php echo $item->getThumbByColumn($model->column_pattern); ?>" alt="">
                        </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-md-12 not-found">
                    <div class="alert alert-warning">
                        Item not found.
                    </div>
                </div>

            <?php endif; ?>

    </div>

</div>

<?php

$urlUpdate = \yii\helpers\Url::to(['update', 'id'=>$model->id], true);

$this->registerJs( <<<JS
    var CKEDITOR   = window.parent.parent.CKEDITOR;
    for ( var i in CKEDITOR.instances ){
       var currentInstance = i;
       break;
    }
    var curEditor   = CKEDITOR.instances[currentInstance];
JS
    , \yii\web\View::POS_HEAD);


$this->registerJs( <<<JS
    $("#edit").on("click",function(){

        //console.log();
        //window.parent.parent.CKE_flexModuleIframeUrl = 'http://localhost/kamol/backend/web/editor/flexible-module/update/';
        window.parent.parent.CKE_flexModuleIframeUrl = '$urlUpdate';
        curEditor.execCommand('openFlexMainDialog');

    });
JS
); ?>