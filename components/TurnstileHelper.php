<?php

namespace obbz\yii2\components;

use Yii;

/**
 * Base helper for Cloudflare Turnstile server-side verification.
 *
 * Usage in view:
 *   <?= TurnstileHelper::widget($this) ?>
 *
 * Usage in controller:
 *   if (!TurnstileHelper::verifyRequest()) { ... }
 */
class TurnstileHelper
{
    const VERIFY_URL = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
    const POST_FIELD  = 'cf-turnstile-response';
    const JS_URL      = 'https://challenges.cloudflare.com/turnstile/v0/api.js';

    /**
     * Verify a Turnstile token string.
     * @param string $token
     * @return bool
     */
    public static function verify($token)
    {
        if (YII_ENV_TEST || YII_ENV_DEV) {
            return true;
        }

        $secret = Yii::$app->params['turnstile.secret_key'] ?? '';

        $ch = curl_init(self::VERIFY_URL);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query([
                'secret'   => $secret,
                'response' => $token,
            ]),
            CURLOPT_TIMEOUT        => 10,
        ]);
        $body = curl_exec($ch);
        curl_close($ch);

        if (!$body) {
            return false;
        }

        $data = json_decode($body, true);
        return !empty($data['success']);
    }

    /**
     * Verify the token submitted in the current request (reads from POST automatically).
     * @return bool
     */
    public static function verifyRequest()
    {
        $token = Yii::$app->request->post(self::POST_FIELD, '');
        return static::verify($token);
    }

    /**
     * Get the Turnstile site key from app params.
     * @return string
     */
    public static function siteKey()
    {
        return Yii::$app->params['turnstile.site_key'] ?? '';
    }

    /**
     * Render the Turnstile widget HTML and register the Cloudflare JS script.
     * Drop-in replacement for <?= $form->field($model, 'verifyCode')->captcha() ?>
     *
     * @param \yii\web\View|null $view  Pass $this from a view to auto-register JS via registerJsFile.
     *                                  If null, an inline <script> tag is appended.
     * @param array $options  Extra HTML attributes for the widget <div>.
     * @return string  HTML ready to echo.
     */
    public static function widget($view = null, array $options = [])
    {
        $siteKey = static::siteKey();
        $attrs = '';
        foreach ($options as $k => $v) {
            $attrs .= ' ' . htmlspecialchars($k, ENT_QUOTES) . '="' . htmlspecialchars($v, ENT_QUOTES) . '"';
        }

        $html = '<div class="cf-turnstile" data-sitekey="' . htmlspecialchars($siteKey, ENT_QUOTES) . '"' . $attrs . '></div>';

        if ($view !== null) {
            $view->registerJsFile(static::JS_URL, [
                'position' => \yii\web\View::POS_HEAD,
                'async'    => true,
                'defer'    => true,
            ]);
            return $html;
        }

        return $html . "\n" . '<script src="' . static::JS_URL . '" async defer></script>';
    }
}
