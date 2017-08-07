<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\utils;



class HttpHandler
{

    /**
     * OK. Everything worked as expected.
     *
     * @param null|array|string $message
     * @param string $title
     * @return null
     */
    public static function ok($message = null, $title = "Successfully"){
        \Yii::$app->response->statusCode = 200;
        return self::abstractResult($message, $title);
    }

    /**
     * The resource was not modified. You can use the cached version.
     *
     * @param null|array|string $message
     * @param string $title
     * @return null
     */
    public static function resourceNotModified($message = null, $title = "The resource was not modified"){
        \Yii::$app->response->statusCode = 304;
        return self::abstractResult($message, $title);
    }

    /**
     * Too many requests. The request was rejected due to rate limiting.
     *
     * @param null|array|string $message
     * @param string $title
     * @return null
     */
    public static function badRequest($message = null, $title = "Bad request"){
        \Yii::$app->response->statusCode = 400;
        return self::abstractResult($message, $title);
    }

    /**
     * Authentication failed.
     *
     * @param null|array|string $message
     * @param string $title
     * @return null
     */
    public static function authenticationFailed($message = null, $title = "Authentication failed"){
        \Yii::$app->response->statusCode = 401;
        return self::abstractResult($message, $title);
    }

    /**
     * The authenticated user is not allowed to access the specified API endpoint.
     *
     * @param null|array|string $message
     * @param string $title
     * @return null
     */
    public static function unauthorized($message = null, $title = "Unauthorized user"){
        \Yii::$app->response->statusCode = 403;
        return self::abstractResult($message, $title);
    }

    /**
     * The requested resource does not exist.
     *
     * @param null|array|string $message
     * @param string $title
     * @return null
     */
    public static function resourceNotFound($message = null, $title = "The requested resource does not exist"){
        \Yii::$app->response->statusCode = 404;
        return self::abstractResult($message, $title);
    }

    /**
     *  Method not allowed. Please check the Allow header for the allowed HTTP methods.
     *
     * @param null|array|string $message
     * @param string $title
     * @return null
     */
    public static function methodNotAllowed($message = null, $title="Method not allowed"){
        \Yii::$app->response->statusCode = 405;
        return self::abstractResult($message, $title);
    }

    /**
     *  Conflict
     *
     * @param null|array|string $message
     * @param string $title
     * @return null
     */
    public static function conflict($message = null, $title="Conflict"){
        \Yii::$app->response->statusCode = 409;
        return self::abstractResult($message, $title);
    }

    /**
     *  Unsupported media type
     *
     * @param null|array|string $message
     * @param string $title
     * @return null
     */
    public static function unsupportedMediaType($message = null, $title="Unsupported media type"){
        \Yii::$app->response->statusCode = 415;
        return self::abstractResult($message, $title);
    }


    /**
     * Data validation failed (in response to a POST request, for example).
     * Please check the response body for detailed error messages.
     *
     * @param array|string $message
     * @param string $title
     * @return array
     */
    public static function validationFail($message, $title="Data validation failed "){
        \Yii::$app->response->statusCode = 422;
        return self::abstractResult($message, $title);
    }



    /**
     * Too many requests. The request was rejected due to rate limiting.
     *
     * @param null|array|string $message
     * @param string $title
     * @return null
     */
    public static function tooManyRequests($message = null, $title="Too many requests"){
        \Yii::$app->response->statusCode = 429;
        return self::abstractResult($message, $title);
    }

    /**
     * Internal server error. This could be caused by internal program errors.
     *
     * @param null|array|string $message
     * @param string $title
     * @return null
     */
    public static function internalError($message = null, $title ="Internal server error"){
        \Yii::$app->response->statusCode = 500;
        return self::abstractResult($message, $title);
    }


    /**
     * for convert message result to array of result always.
     * @param $message
     * @param string $title
     * @return array
     */
    public static function abstractResult($message, $title = ""){
        if(is_string($message)){

            return (object)[
                "title"=>empty($title) ? $message : $title,
                "message"=>$message
            ];
        }
        else if(is_array($message)){ // array
//            return json_encode($message, JSON_PRETTY_PRINT);
            return $message;
        }else{
            return $message;
        }
    }
}