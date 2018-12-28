<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\behaviors;

use yii\base\Behavior;

class VisitCounterBehavior extends Behavior
{
    public $attribute = 'view_count';
    public $expire = 3600; //default is 3600 = 1 hour if 0 is always update

    public function init()
    {
        parent::init();

        if ($this->attribute === null) {
            throw new InvalidConfigException('The "attribute" property must be set.');
        }

    }

    public function updateViewCounter(){
        $expire = $this->expire;
        $attribute = $this->attribute;
        if($expire > 0){
            $requestCookie = \Yii::$app->request->cookies;
            $ctName = 'v_'.$this->owner->tableName(). '_'. $attribute . $this->owner->id;
            /** real counter */
            if (!$requestCookie->has($ctName)){
                $responseCookie = \Yii::$app->response->cookies;
                $responseCookie->add(new \yii\web\Cookie([
                    'name' => $ctName,
                    'value' => true,
                    'expire' => time()+ $expire,
                ]));
                $this->owner->updateCounters([$attribute => 1]);
            }
        }else{
            $this->owner->updateCounters([$attribute => 1]);
        }
    }



}