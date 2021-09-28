<?php
/**
 * @link http://www.diemeisterei.de
 * @copyright Copyright (c) 2019 diemeisterei GmbH, Stuttgart
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace obbz\yii2\widgets\cookieconsent;

use yii\web\AssetBundle;

class CookieConsentAsset extends AssetBundle
{

    public $sourcePath = __DIR__;

    public $css = [
        'assets/cookie-consent.css',
    ];

    public $js = [
        'assets/cookie-consent.js',
    ];

//    public $depends = [
//    ];

}
