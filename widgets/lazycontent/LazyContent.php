<?php
/**
 * User: mattakorn
 * Date: 21/8/2564
 */

namespace obbz\yii2\widgets\lazycontent;


use yii\base\Widget;

class LazyContent extends Widget
{
    public $ajaxUrl = '';
    public $containerId;
    public $loadingHtml = '<div class="lazy-content-loading"><i class="fas fa-circle-notch fa-spin"></i></div>';
    public $afterAjaxDone = '';

    public $activeOffset = '90%';

    // using for allow need to load after depented widget
    public $dependWidgetId;
    public $dependDelaySecond = 100;

    public function init()
    {
        parent::init();
        if (empty($this->ajaxUrl)) {
            throw new InvalidConfigException('Invalid configuration to property "ajaxUrl"');
        }

        $this->containerId  = isset($this->containerId)? : 'container_' . $this->id;
    }


    public function run()
    {
        $view = $this->getView();
        $this->registerAssets($view);
        return '<div id="'. $this->id .'" class="lazy-content">
                    <div id="'.  $this->containerId  .'"></div>
                </div>';
    }


    public function registerAssets($view)
    {
        LazyContentAsset::register($view);
        $isLoaded = 'isLoad_' . $this->id;
        $isLoadedDelay = 'isLoadDelay_' . $this->id;
        $waypointObj = 'wp_' . $this->id;
        $beforeHandle = '';
        if($this->dependWidgetId){
            $dependIsLoadedVar = 'isLoadDelay_' . $this->dependWidgetId;
            $beforeHandle = 'if(' . $dependIsLoadedVar . ' == false) return;';
        }

        $view->registerJs(<<<JS
            var $isLoaded = false;
            var $isLoadedDelay = false;
            var $waypointObj = new Waypoint({
              element: document.getElementById("$this->containerId"),
              offset: '$this->activeOffset',
              handler: function(direction) {
                 if($isLoaded == false){
                    $beforeHandle
                    $("#$this->containerId").html('$this->loadingHtml');

                    $.ajax({
                      url: "$this->ajaxUrl"
                    })
                      .done(function( data ) {
                         $("#$this->containerId").html(data);
                         $this->afterAjaxDone

                         Waypoint.refreshAll()
                         $isLoaded = true;
                         setTimeout(function() {
                                $isLoadedDelay = true;
                         }, $this->dependDelaySecond);

                      });

                 }


              }
            });
JS
            , \yii\web\View::POS_READY
        );

    }

}