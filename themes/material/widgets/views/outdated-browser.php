<?php
/**
 * @author: Mattakorn Limkool
 *
 */
use obbz\yii2\utils\ObbzYii;

?>

<!--[if lt IE 9]>

<div class="ie-warning">
    <h1 class="c-white"><?php echo \Yii::t('obbz', 'Warning!!'); ?></h1>
    <p><?php echo \Yii::t('obbz', 'You are using an outdated version of Internet Explorer, please upgrade'); ?>
        <br/><?php echo \Yii::t('obbz', 'to any of the following web browsers to access this website.'); ?></p>
    <div class="iew-container">
        <ul class="iew-download">
            <li>
                <a href="http://www.google.com/chrome/">
                    <img src="<?php echo ObbzYii::assetBaseUrl('img/browsers/chrome.png'); ?>" alt="">
                    <div>Chrome</div>
                </a>
            </li>
            <li>
                <a href="https://www.mozilla.org/en-US/firefox/new/">
                    <img src="<?php echo ObbzYii::assetBaseUrl('img/browsers/firefox.png'); ?>" alt="">
                    <div>Firefox</div>
                </a>
            </li>
            <li>
                <a href="http://www.opera.com">
                    <img src="<?php echo ObbzYii::assetBaseUrl('img/browsers/opera.png'); ?>" alt="">
                    <div>Opera</div>
                </a>
            </li>
            <li>
                <a href="https://www.apple.com/safari/">
                    <img src="<?php echo ObbzYii::assetBaseUrl('img/browsers/safari.png'); ?>" alt="">
                    <div>Safari</div>
                </a>
            </li>
            <li>
                <a href="http://windows.microsoft.com/en-us/internet-explorer/download-ie">
                    <img src="<?php echo ObbzYii::assetBaseUrl('img/browsers/ie.png'); ?>" alt="">
                    <div>IE (New)</div>
                </a>
            </li>
        </ul>
    </div>
    <p><?php echo \Yii::t('obbz', 'Sorry for the inconvenience!'); ?></p>
</div>
<![endif]-->