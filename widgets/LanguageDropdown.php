<?php
/**
 * Created by PhpStorm.
 * User: mattakorn
 * Date: 10/23/2019
 * Time: 22:18
 */
namespace obbz\yii2\widgets;

use Yii;
use obbz\yii2\utils\ObbzYii;
use yii\bootstrap\Dropdown;

class LanguageDropdown extends Dropdown
{
    private static $_labels;

    private $_isError;

    public $label;
    public $labelPrefix;
    public $labelSuffix;

    public $wrapper = '<div class="dropdown">{template}</div>';
    public $template = '<a href="#" data-toggle="dropdown" class="dropdown-toggle">{labelPrefix}{label}{labelSuffix}</a>
                        {items}';

    public function init()
    {
        $route = Yii::$app->controller->route;
        $appLanguage = Yii::$app->language;
        $params = $_GET;
        $this->_isError = $route === Yii::$app->errorHandler->errorAction;

        array_unshift($params, '/' . $route);

        if($this->label == null){
            $this->label = $this->label($appLanguage);
        }

        foreach (Yii::$app->urlManager->languages as $language) {
            $isWildcard = substr($language, -2) === '-*';
            if (
                $language === $appLanguage ||
                // Also check for wildcard language
                $isWildcard && substr($appLanguage, 0, 2) === substr($language, 0, 2)
            ) {
                continue;   // Exclude the current language
            }
            if ($isWildcard) {
                $language = substr($language, 0, 2);
            }
            $params['language'] = $language;
            $this->items[] = [
                'label' => self::label($language),
                'url' => $params,
            ];
        }
//        ObbzYii::debug( $this->items);
        parent::init();
    }

    public function run()
    {

        // Only show this widget if we're not on the error page
        if ($this->_isError) {
            return '';
        } else {
            $items = parent::run();
            $label = $this->label;
            $template = str_replace([
                '{label}', '{items}', '{labelPrefix}', '{labelSuffix}'],
                [$label, $items, $this->labelPrefix, $this->labelSuffix],
                $this->template
            );
            $result = str_replace('{template}', $template, $this->wrapper);
            return $result;
        }
    }

    public static function label($code)
    {
        if (self::$_labels === null) {
            self::$_labels = ObbzYii::app()->params['languages'];
        }

        return isset(self::$_labels[$code]) ? self::$_labels[$code] : null;
    }
}