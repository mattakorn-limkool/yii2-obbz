<?php
use yii\helpers\Html;
use obbz\yii2\themes\material\widgets\ActiveForm;
use obbz\yii2\utils\ObbzYii;
/**
 * @author: Mattakorn Limkool
 * @var $model \obbz\yii2\models\ServerStatus
 */

$this->title =   \Yii::t('obbz', 'Server Status') . ' ('. Yii::$app->name .')';
$storageBar = $model->storageSizeProgressBar();
$bandwidthBar = $model->bandwidthProgressBar();
?>

<div class="card server-status-view">
    <div class="card-header ch-alt">
        <h2><?php echo Html::encode($this->title) ?></h2>
    </div>
    <?php $form = ActiveForm::begin(); ?>
    <div class="card-body card-padding translate-form ">
        <div class="row">
            <div class="col-md-12">
                <h4>Storage</h4>

                <div class="progress m-b-10">
                    <div class="progress-bar <?php echo $storageBar['textProgress'] ?>" role="progressbar"
                         aria-valuenow="<?php echo $storageBar['usagePercent'] ?>" aria-valuemin="0"
                         aria-valuemax="<?php echo $storageBar['maxPercent'] ?>"
                         style="width: <?php echo $storageBar['usagePercent'] ?>%">

                    </div>
                </div>
                <span class="<?php echo $model->overMaxStorageSize > 0 ? 'text-danger' : ''; ?>">
                    <?php echo ObbzYii::formatter()->asShortSize($model->currentStorageSize) ?>
                </span>
                /
                <?php echo ObbzYii::formatter()->asShortSize($model->maxStorageSize) ?>
                <br>
                <br>

                <?php if($model->overMaxStorageSize > 0): ?>
                    <div class="alert alert-danger">
                        <p>
                            <i class="fa fa-warning"></i>  Over Storage size limit: <?php echo ObbzYii::formatter()->asShortSize($model->overMaxStorageSize) ?>
                        </p>
                        <p>
                            Please expand max storage as soon as posible, before system will automatically delete files permanently.
                        </p>

                    </div>
                <?php endif; ?>


                <ul class="clist clist-angle">
                    <?php foreach($model->getStorages() as $storage): ?>
                        <li class="<?php echo $storage['show'] ? '': 'hidden'; ?>"
                            >
                            <b><?php echo $storage['name'] ?></b>: <?php echo ObbzYii::formatter()->asShortSize( $storage['size']) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <br>
                <hr>
            </div>


            <?php if($model->currentBandwidth > 0): ?>
                <div class="col-md-12">
                    <h4>Bandwidth</h4>

                    <div class="progress m-b-10">
                        <div class="progress-bar <?php echo $bandwidthBar['textProgress'] ?>" role="progressbar"
                             aria-valuenow="<?php echo $bandwidthBar['usagePercent'] ?>" aria-valuemin="0"
                             aria-valuemax="<?php echo $bandwidthBar['maxPercent'] ?>"
                             style="width: <?php echo $bandwidthBar['usagePercent'] ?>%">

                        </div>
                    </div>
                    <span class="<?php echo $model->overMaxBandwidth > 0 ? 'text-danger' : ''; ?>">
                        <?php echo ObbzYii::formatter()->asShortSize($model->currentBandwidth) ?>
                    </span>
                    /
                    <?php echo ObbzYii::formatter()->asShortSize($model->maxBandwidth) ?>
                    <br>
                    <br>

                    <?php if($model->overMaxBandwidth > 0): ?>
                        <div class="alert alert-danger">
                            <p>
                                <i class="fa fa-warning"></i>  Over Bandwidth limit: <?php echo ObbzYii::formatter()->asShortSize($model->overMaxBandwidth) ?>
                            </p>
                            <p>
                                Please expand your server for support more bandwidth.
                            </p>

                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>


        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>

