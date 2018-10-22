<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\actions;
use obbz\yii2\modules\VoteModule;
use hauntd\vote\events\VoteActionEvent;
use hauntd\vote\models\Vote;
use hauntd\vote\models\VoteAggregate;
use obbz\yii2\models\VoteForm;
use hauntd\vote\traits\ModuleTrait;
use obbz\yii2\utils\ObbzYii;
use yii\helpers\Url;
use yii\web\MethodNotAllowedHttpException;
use yii\web\Response;

class VoteAction extends \hauntd\vote\actions\VoteAction
{
    /**
     * @return array
     * @throws MethodNotAllowedHttpException
     */
    public function run()
    {
        if (!\Yii::$app->request->getIsAjax() || !\Yii::$app->request->getIsPost()) {
            throw new MethodNotAllowedHttpException(\Yii::t('vote', 'Forbidden method'), 405);
        }

        \Yii::$app->response->format = Response::FORMAT_JSON;
        $module = $this->getModule();
        $form = new VoteForm();
        $form->load(\Yii::$app->request->post());
        $this->trigger(self::EVENT_BEFORE_VOTE, $event = $this->createEvent($form, $response = []));

        if ($form->validate()) {
            $settings = $module->getSettingsForEntity($form->entity);
            if ($settings['type'] == VoteModule::TYPE_VOTING) {
                $response = $this->processVote($form);
            } else {
                $response = $this->processToggle($form);
            }
            $response = array_merge($event->responseData, $response);
            $response['aggregate'] = VoteAggregate::findOne([
                'entity' => $module->encodeEntity($form->entity),
                'target_id' => $form->targetId
            ]);
        } else {
            // redirect when guest
            $settings = $module->getSettingsForEntity($form->entity);

            if($form->doRedirectLogin && $settings['allowGuests'] == false){
                ObbzYii::setFlashError('Please Login before');
                $this->controller->redirect(Url::to($module->redirectUrl));
            }
            $response = ['success' => false, 'errors' => $form->errors];
        }

        $this->trigger(self::EVENT_AFTER_VOTE, $event = $this->createEvent($form, $response));

        return $event->responseData;
    }
}