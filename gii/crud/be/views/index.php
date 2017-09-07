<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use obbz\yii2\utils\ObbzYii;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();
$nameAttribute = $generator->getNameAttribute();

echo "<?php\n";
?>

use yii\helpers\Html;
use obbz\yii2\widgets\ButtonLink;
use obbz\yii2\utils\ObbzYii;
use <?= $generator->indexWidgetType === 'grid' ? "yii\\grid\\GridView" : "yii\\widgets\\ListView" ?>;
<?= $generator->enablePjax ? 'use yii\widgets\Pjax;' : '' ?>

/* @var $this yii\web\View */
<?= !empty($generator->searchModelClass) ? "/* @var \$searchModel " . ltrim($generator->searchModelClass, '\\') . " */\n" : '' ?>
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-index">
    <div class="card">
        <div class="card-header ch-alt ">
			<h2><?= "<?= " ?>Html::encode($this->title) ?></h2>
			<?= '<?php /*echo \obbz\yii2\themes\material\widgets\CircleButtonLink::widget([
					"icon"=> "plus",
					"url"=>["create"],
					"toggleText"=> "Create " . $this->
				]);*/ ?>'  ?>
			
				<ul class="actions ">
					<li><?= '<?php echo ButtonLink::widget([
							"text"=>"Create",
							"url"=>["create"],
							"btnClass"=>"success",
							"prefixIcon" => "plus",
						]); ?>'."\n" ?>
					</li>
<!--		            <li>-->
<!--		                <a href="">-->
<!--		                    <i class="zmdi zmdi-plus"></i>-->
<!--		                </a>-->
<!--		            </li>-->
		        </ul>
		</div>
		<div class="card-body ">
<?= $generator->enablePjax ? "    		<?php Pjax::begin(['timeout' => 5000]); ?>\n" : '' ?>
<?php if(!empty($generator->searchModelClass)): ?>
<?= "    		<?php " . ($generator->indexWidgetType === 'grid' ? "// " : "") ?>echo $this->render('_search', ['model' => $searchModel]); ?>
<?php endif; ?>


<?php if ($generator->indexWidgetType === 'grid'): ?>
			<div class="table-responsive">
    <?= "		<?= " ?>\obbz\yii2\widgets\grid\CoreGridView::widget([
				'dataProvider' => $dataProvider,
                //'sortableEnable'=>false,
				'enableSelectedAction'=>false,
				<?= !empty($generator->searchModelClass) ? "//'filterModel' => \$searchModel,\n        		'columns' => [\n" : "'columns' => [\n"; ?>
					// ['class' => 'yii\grid\SerialColumn'],

<?php
$count = 0;
$showAttributes = ['title', 'detail', 'img'];
if (($tableSchema = $generator->getTableSchema()) === false) {
    foreach ($generator->getColumnNames() as $name) {
        if (in_array($name, $showAttributes)) {
            echo "            		'" . $name . "',\n";
        } else {
            echo "            		// '" . $name . "',\n";
        }
    }
} else {
    foreach ($tableSchema->columns as $column) {
        $format = $generator->generateColumnFormat($column);
        if (in_array($column->name, $showAttributes)) {
            echo "            		'" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
        } else {
            echo "            		// '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
        }
    }
}
?>
					/*[
						'attribute' => 'statusPublish',
						'format' => 'html',
						'value' => function ($model) { return  $model->displayPublishStatus(); },
					],*/
					[
						'class' => obbz\yii2\widgets\grid\CoreActionColumn::className(),
						'enableHeaderAction'  => false,
					],
                ],
            ]); ?>
<?php else: ?>
			<div class="list-view">
    <?= "		<?= " ?>ListView::widget([
				'dataProvider' => $dataProvider,
				'itemOptions' => ['class' => 'item'],
				'itemView' => function ($model, $key, $index, $widget) {
					return Html::a(Html::encode($model-><?= $nameAttribute ?>), ['view', <?= $urlParams ?>]);
				},
			]) ?>
<?php endif; ?>
<?= $generator->enablePjax ? "    		<?php Pjax::end(); ?>\n" : '' ?>

			</div>
		</div>
	</div>
</div>
