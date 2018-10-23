<?php
/**
 * @author: Mattakorn Limkool
 *
 */

echo kartik\rating\StarRating::widget([
    'name' => 'rating_value',
    'value' => $userValue,
    'pluginOptions' => $pluginOptions,
    'pluginEvents' => $pluginEvents,
]);
?>

