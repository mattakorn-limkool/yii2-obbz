<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\extensions\ckeditor;

use yii\helpers\ArrayHelper;


trait CoreCKEditorTrait
{
    /**
     * @var string the toolbar preset. It can be any of the following:
     *
     * - basic: will load the configuration on presets/basic.php
     * - full: will load the configuration on presets/full.php
     * - standard: will load the configuration on presets/standard.php
     * - custom: configuration will be based on [[clientOptions]].
     *
     * Defaults to 'standard'. It is important to note that any configuration item of the loaded presets can be
     * overrided by [[clientOptions]]
     */
    public $preset = 'standard';

    /**
     * @var array the options for the CKEditor 4 JS plugin.
     * Please refer to the CKEditor 4 plugin Web page for possible options.
     * @see http://docs.ckeditor.com/#!/guide/dev_installation
     */
    public $clientOptions = [];

    /**
     * Initializes the widget options.
     * This method sets the default values for various options.
     */
    protected function initOptions()
    {
        $options = [];
        switch ($this->preset) {
//            case 'custom':
//                $preset = null;
//                break;
            case 'basic':
            case 'full':
            case 'standard':
                $preset = 'presets/' . $this->preset . '.php';
                break;
            default:
                $preset = $this->preset;
        }
        if ($preset !== null) {
            $options = require(\Yii::getAlias($preset));
        }
        $this->clientOptions = ArrayHelper::merge($options, $this->clientOptions);
    }
}