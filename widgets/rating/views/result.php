<?php
/**
 * @author: Mattakorn Limkool
 * @var $userValue
 * @var \obbz\yii2\models\RatingAggregate $ratingAggregate
 */
?>
<style>
    .rating-result{}
    .rating-result .score-body{
        text-align: center;
    }
    .rating-result .score-body .score{
        font-size: 30px;
    }
    .rating-result .score-body .score-total{
        font-size: 25px;
        color: #bbb;
    }
    .rating-result .result-body{
    }
    .rating-result .result-body .item{
        float:left;
    }
    .rating-result .result-body .item .star-label{
        float: left;
        width: 50px;
        line-height: 21px;
        height: 19px;
        font-size: 16px;
        text-align: right;
        margin-right: 7px;
    }
    .rating-result .result-body .item .star-label .fa-star{
        color: #fde16d;
        -webkit-text-stroke: 1px #777;
    }
    .rating-result .result-body .item .progress-size{
        width:180px;
        float:left;
    }
    .rating-result .result-body .item .progress{
        height:12px;
        margin:8px 0;
    }
    .rating-result .result-body .item .amount{
        float:right;
        margin-left:10px;
    }
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



