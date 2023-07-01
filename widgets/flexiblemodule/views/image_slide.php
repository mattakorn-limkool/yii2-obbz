<?php
/**
 * @author  Mattakorn Limkool <obbz.dev@gmail.com>
 * @var $model \obbz\yii2\admin\models\FlexibleModule
 * @var $defaultCssClass string
 */
$colCount = 1;
foreach($model->columnPatterns as $key => $model->columnPatterns){
    if($key == $model->column_pattern)
        break;
    $colCount++;
}

?>

<div class="images-slider" data-items="<?= $colCount ?>">
    <?php foreach($model->getFeRelateItems() as $item):?>
        <div class="image-item">
            <img src="<?= $item->getThumbByColumn($model->column_pattern); ?>" alt="" class="img-fluid">
        </div>
    <?php endforeach; ?>


</div>




