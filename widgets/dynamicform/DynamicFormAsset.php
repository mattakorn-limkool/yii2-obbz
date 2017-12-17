<?php


namespace obbz\yii2\widgets\dynamicform;

class DynamicFormAsset extends \wbraganca\dynamicform\DynamicFormAsset
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->setSourcePath(__DIR__ . '/assets');
        $this->setupAssets('js', ['yii2-dynamic-form']);
        parent::init();
    }
}
