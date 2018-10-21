<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\widgets;


use kop\y2sp\ScrollPager;
use yii\widgets\ListView;

class InfListView extends ListView
{
    public $summary = false;
    public $itemOptions = ['class'=>'item'];
    public $pagerAdditional = [];
    public function init(){
        $this->pager =array_merge([
            'class' => ScrollPager::class,
            'triggerTemplate' => '<div class="ias-trigger" style="text-align: center; cursor: pointer;">
                                    <div class="load-more">
                                        <a><i class="zmdi zmdi-refresh-alt"></i> {text}</a>
                                    </div>

                                    </div>',
            'noneLeftText' => '',
            'triggerText' => 'Load more...',
            'enabledExtensions' => [
                ScrollPager::EXTENSION_TRIGGER,
                ScrollPager::EXTENSION_SPINNER,
                ScrollPager::EXTENSION_NONE_LEFT,
                ScrollPager::EXTENSION_PAGING
            ],
        ], $this->pagerAdditional);
        parent::init();
    }

    protected function renderDataCellContent($model, $key, $index)
    {
        $pagination = $this->dataProvider->getPagination();
        if ($pagination !== false) {
            return $pagination->getOffset() + $index + 1;
        } else {
            return $index + 1;
        }
    }
}