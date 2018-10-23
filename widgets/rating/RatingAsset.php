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

    public $js = [
    ];

    public $css = [
        'rating.css',
    ];

    public $depends = [
        'yii\web\JqueryAsset'
    ];
}