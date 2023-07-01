<?php
/**
 * @author  Mattakorn Limkool <obbz.dev@gmail.com>
 * @var $model \obbz\yii2\admin\models\FlexibleModule
 * @var $defaultCssClass string
 */
?>

<div class="row featuredContainer zoom-gallery">
    <?php foreach($model->getFeRelateItems() as $item):?>
        <div class="<?= $model->column_pattern ?>">
            <div class="gallery-box-layout1">
                <img src="<?= $item->getThumbByColumn($model->column_pattern); ?>" alt="<?= $item->title ?>" class="img-fluid">
                <div class="item-icon">
                    <a href="<?= $item->getFullThumb(); ?>" class="popup-zoom" data-fancybox-group="gallery<?= $model->id ?>" title="">
                        <i class="flaticon-search"></i>
                    </a>
                </div>
<!--                <div class="item-content">-->
<!--                    <h3 class="item-title">Modern Clinic</h3>-->
<!--                    <span class="title-ctg">Cancer Care, Cardiac</span>-->
<!--                </div>-->
            </div>
        </div>
    <?php endforeach; ?>
</div>





