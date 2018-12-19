<?php

namespace obbz\yii2\themes\material;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class MaterialAsset extends AssetBundle
{
    public $sourcePath = '@vendor/obbz/yii2/themes/material/assets';
    const THEME = "default";
//    public $disableMainTitle = true;

    public function init(){
        $this->css = [
            //### theme ###
//        'vendors/bower_components/fullcalendar/dist/fullcalendar.min.css',
            'vendors/bower_components/animate.css/animate.min.css',
//            'vendors/bower_components/bootstrap-sweetalert/lib/sweet-alert.css',
            'vendors/bower_components/material-design-iconic-font/dist/css/material-design-iconic-font.min.css',
            'vendors/bower_components/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.min.css',
            'vendors/bower_components/lightgallery/light-gallery/css/lightGallery.css',
//        'vendors/bootgrid/jquery.bootgrid.min.css',
            'css/app.css',
            'css/custom.css',
            'css/themes/'. static::THEME . '.css',
//        'css/app.min.1.css',
//        'css/app.min.2.css',

            // external font by google
            'https://fonts.googleapis.com/css?family=Kanit',

        ];
        parent::init();
    }

    public $js = [
        //### theme ###
//        'vendors/bower_components/jquery/dist/jquery.min.js',
//        'vendors/bower_components/bootstrap/dist/js/bootstrap.min.js',
        'vendors/bower_components/flot/jquery.flot.js',
        'vendors/bower_components/flot/jquery.flot.resize.js',
        'vendors/bower_components/flot.curvedlines/curvedLines.js',
        'vendors/sparklines/jquery.sparkline.min.js',
//        'vendors/bower_components/jquery.easy-pie-chart/dist/jquery.easypiechart.min.js',

//        'vendors/bower_components/moment/min/moment.min.js',
//        'vendors/bower_components/fullcalendar/dist/fullcalendar.min.js',
//        'vendors/bower_components/fullcalendar/dist/lang-all.js',
//        'vendors/bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js',

        'vendors/bower_components/simpleWeather/jquery.simpleWeather.min.js',
        'vendors/bower_components/Waves/dist/waves.min.js',
//        'vendors/bootstrap-growl/bootstrap-growl.min.js',
//        'vendors/bower_components/bootstrap-sweetalert/lib/sweet-alert.min.js',
        'vendors/bower_components/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.js',

//        'vendors/bower_components/lightgallery/light-gallery/js/lightGallery.js',
        'vendors/bower_components/lightGallery139/dist/js/lightgallery.js',
//        'vendors/fileinput/fileinput.min.js',
        'js/flot-charts/curved-line-chart.js',
        'js/flot-charts/line-chart.js',
        'js/charts.js',

        'js/functions.js',
//        'js/demo.js',

        'js/common.js',

    ];
//    public $jsOptions = [
//        'position'=>self::PO
//    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'rmrevin\yii\fontawesome\AssetBundle',
    ];

}
