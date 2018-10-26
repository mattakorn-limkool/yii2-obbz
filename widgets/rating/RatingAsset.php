<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\widgets\rating;


use yii\web\AssetBundle;

class RatingAsset extends AssetBundle
{
    public $sourcePath = '@vendor/obbz/yii2/widgets/rating/assets';


    public $css = [
        'rating.css',
    ];

    public $js = [
    ];


    public $depends = [
        'yii\web\JqueryAsset'
    ];
}