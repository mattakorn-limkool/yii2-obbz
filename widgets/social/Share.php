<?php
namespace obbz\yii2\widgets\social;
use obbz\yii2\utils\ObbzYii;
use yii\helpers\Json;

/**
 * @author: Mattakorn Limkool
 * @see http://js-socials.com
 */
class Share extends \yii\base\Widget
{
    /**  SHARE TYPE */
    const SHARE_EMAIL = 'email';
    const SHARE_TWITTER = 'twitter';
    const SHARE_FACEBOOK = 'facebook';
    const SHARE_VKONTAKTE = 'vkontakte';
    const SHARE_GOOGLE_PLUS = 'googleplus';
    const SHARE_LINKEDIN = 'linkedin';
    const SHARE_PINTEREST = 'pinterest';
    const SHARE_STUBLEUPON = 'stumbleupon';
    const SHARE_TELEGRAM = 'telegram';
    const SHARE_WHATSAPP = 'whatsapp';
    const SHARE_LINE = 'line';
    const SHARE_VIBER = 'viber';
    const SHARE_POCKET = 'pocket';
    const SHARE_MESSENGER = 'messenger';

    /**
     * SHARE IN
     */
    const SHAREIN_POPUP = 'popup';
    const SHAREIN_BLANK = 'blank';
    const SHAREIN_SELF = 'self';

    /** THEMES */
    const THEME_CLASSIC = 'classic';
    const THEME_FLAT = 'flat';
    const THEME_MINIMA = 'minima';
    const THEME_PLAIN = 'plain';

    /**
     * @var array
     */
    public $shares = [self::SHARE_FACEBOOK, self::SHARE_LINE, self::SHARE_TWITTER, self::SHARE_EMAIL];

    /**
     * @var string
     */
    public $theme = self::THEME_FLAT;

    /**
     * @var array
     */
    public $pluginOptions = [
        'showLabel' => false,
        'showCount' => false,
        'shareIn' => self::SHAREIN_POPUP, // "blank"|"popup"|"self"
    ];


    public $viewFile = '@vendor/obbz/yii2/widgets/social/views/share';

    public function init(){

        $assetBundle = ShareAsset::register($this->view);
        if($this->theme){
            $assetBundle->css[] = 'jssocials-theme-'. $this->theme .'.css';
        }

        $this->registerScript();
        parent::init();
    }

    public function run(){
        return $this->render($this->viewFile, [
            'widgetId' => $this->id,
        ]);
    }

    protected function registerScript()
    {
        $widgetId = $this->id;
        $config = $this->pluginOptions + ['shares' => $this->shares];
        $configJson = Json::encode($config);
        $js = "jQuery('#{$widgetId}').jsSocials($configJson);";
        $this->getView()->registerJs($js);
    }


    public static function registerMetaTags($seoParams){
        foreach($seoParams as $key=>$param){
            if($param != null){
                \Yii::$app->view->registerMetaTag(['name'=>$key, 'content'=>$param], $key);
            }
        }
    }
}