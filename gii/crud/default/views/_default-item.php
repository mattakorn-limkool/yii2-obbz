<?php
use yii\helpers\Html;
use obbz\yii2\utils\ObbzYii;


/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

/* @var $model \yii\db\ActiveRecord */
$model = new $generator->modelClass();
$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
    $safeAttributes = $model->attributes();
}
// unset image
if(($key = array_search('image', $safeAttributes)) !== false) {
    unset($safeAttributes[$key]);
}


$coreAttributes = method_exists($model, 'getCoreAttributes') ?  $model->getCoreAttributes() : [];
$commentAttributes = $coreAttributes;
// unset title
if(($key = array_search('title', $commentAttributes)) !== false) {
    unset($commentAttributes[$key]);
}
// unset detail
if(($key = array_search('detail', $commentAttributes)) !== false) {
    unset($commentAttributes[$key]);
}

echo "<?php\n";
?>

use yii\helpers\Html;
use obbz\yii2\utils\ObbzYii;
use obbz\yii2\utils\ArrayHelper;
use yii\helpers\Url;
/**
* @var $this yii\web\View
* @var $model <?= ltrim($generator->modelClass, '\\') . "\n" ?>
* @var $columnClass string
*/

$viewLink = Url::to(["view", "id"=>$model->id]);
?>

<div class="card">
    <a class="image" href="<?= '<?php echo $viewLink; ?>' ?>">
        <img src="<?= '<?php echo $model->getThumbUploadUrl(\'image\'); ?>' ?>" />
    </a>
    <div class="content">
        <h4 class="title">
            <?= '<?php echo Html::a($model->title, $viewLink); ?>' ?>
        </h4>
        <p class="detail">
            <?= '<?php echo $model->detail; ?>' ?>
        </p>
    </div>

</div>