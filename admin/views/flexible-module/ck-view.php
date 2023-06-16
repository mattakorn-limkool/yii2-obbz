<?php
/**
 * @author  Mattakorn Limkool <obbz.dev@gmail.com>
 *
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
        border-radius: 4px;
        background: #f7f7f7;
        text-align: center;
        cursor: pointer;
        padding: 10px;
        box-shadow: 7px 7px 15px #dddada;
    }
    .flexible-module-iframe .title{
        font-size: 15px;
        font-weight: bold;
        padding: 0 0 5px;
    }
    .flexible-module-iframe .detail{
        font-size: 14px;
    }
</style>

<div class="flexible-module-iframe">
    <span class="title"><?php echo $title; ?> </span>
    <button id="edit" type="button" class="btn btn-primary btn-xs">Edit</button>
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