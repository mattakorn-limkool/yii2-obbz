<?php
/**
 * @author: Mattakorn Limkool
 * @var $userValue
 * @var \obbz\yii2\models\RatingAggregate $ratingAggregate
 */
?>
<style>

</style>
<div id="<?php echo $widgetId ?>" class="rating-result">
    <div class="row">
        <div class="col-md-7">
            <div class="result-body">
                <?php foreach($resultItems as $rateItem): ?>
                <div class="item">
                    <div class="star-label">
                        <?php echo $rateItem['label'] ?> <i class="fa fa-star"></i>

                    </div>
                    <div class="progress-size">
                        <div class="progress" >
                            <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="5" aria-valuemin="0" aria-valuemax="5"
                                 style="width: <?php echo $rateItem['percent'] ?>%">

                            </div>
                        </div>
                    </div>
                    <div class="amount" ><?php echo $rateItem['amount'] ?></div>
                </div>
                <?php endforeach; ?>


            </div>
        </div>
        <div class="col-md-5">
            <div class="score-body">
                Average user rating <br>
                <span class="score"><?php echo number_format($ratingAggregate->rating,1); ?></span>
                <span class="score-total">/ <?php echo $maxValue; ?></span>
            </div>
        </div>
    </div>



</div>



