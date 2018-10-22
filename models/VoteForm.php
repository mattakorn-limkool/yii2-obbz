<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\models;
use hauntd\vote\Module;
use yii\helpers\ArrayHelper;
use hauntd\vote\traits\ModuleTrait;

class VoteForm extends \hauntd\vote\models\VoteForm
{
    public $doRedirectLogin = false;

    /**
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function checkModel()
    {
        $module = $this->getModule();
        $settings = $module->getSettingsForEntity($this->entity);
        if ($settings === null) {
            $this->addError('entity', \Yii::t('vote', 'This entity is not supported.'));
            return false;
        }
        $allowGuests = ArrayHelper::getValue($settings, 'allowGuests', false);
        if (\Yii::$app->user->isGuest && ($settings['type'] == Module::TYPE_TOGGLE || !$allowGuests)) {
            $this->addError('entity', \Yii::t('vote', 'Guests are not allowed for this voting.'));
            $this->doRedirectLogin = true;
            return false;
        }
        $targetModel = \Yii::createObject($settings['modelName']);
        $entityModel = $targetModel->findOne([$targetModel::primaryKey()[0] => $this->targetId]);
        if ($entityModel == null) {
            $this->addError('targetId', \Yii::t('vote', 'Target model not found.'));
            return false;
        }
        $allowSelfVote = ArrayHelper::getValue($settings, 'allowSelfVote', false);
        if (!$allowSelfVote) {
            $entityAuthorAttribute = ArrayHelper::getValue($settings, 'entityAuthorAttribute', 'user_id');
            if (!\Yii::$app->user->isGuest && \Yii::$app->user->id == $entityModel->{$entityAuthorAttribute}) {
                $this->addError('entity', \Yii::t('vote', 'Self-voting are not allowed.'));
                return false;
            }
        }

        return true;
    }
}