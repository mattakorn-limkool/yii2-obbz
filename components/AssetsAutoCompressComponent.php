<?php
namespace obbz\yii2\components;
use obbz\yii2\utils\ObbzYii;
use yii\base\BootstrapInterface;
use yii\web\View;

/**
 * @author: Mattakorn Limkool
 *
 */
class AssetsAutoCompressComponent extends \skeeks\yii2\assetsAuto\AssetsAutoCompressComponent implements BootstrapInterface
{
    // default coz is cannot compress
    public $ignoreAssetsJs = [
        '\dosamigos\ckeditor\CKEditorAsset',
        '\dosamigos\ckeditor\CKEditorWidgetAsset',
    ];

    public $htmlCompressOptions = [
        'extra'         => true,
        'no-comments'   => true
    ];

    protected function _processing(View $view)
    {
        // handle ignore js files
        $ignoreJsFiles = [];
        foreach($this->ignoreAssetsJs as $assetName){
            $bundle = \Yii::$app->getAssetManager()->getBundle($assetName);
            foreach($bundle->js as $jsFile){
                $ignoreJsFiles[$bundle->baseUrl . '/' . $jsFile] = null;
            }
        }


//        ObbzYii::debug($ignoreJsFiles, false);
//        ObbzYii::debug($view->jsFiles);

        if ($view->jsFiles && $this->jsFileCompile)
        {
            \Yii::beginProfile('Compress js files');
            foreach ($view->jsFiles as $pos => $files)
            {
                if ($files)
                {
                    $posIgnoreFile = [];
                    foreach($ignoreJsFiles as $ignoreJsFile => $conf){
                        if(array_key_exists($ignoreJsFile, $files)){
                            unset($files[$ignoreJsFile]);
                            $posIgnoreFile[$ignoreJsFile] = null;
                        }

                    }
                    $view->jsFiles[$pos] = $this->_processingJsFiles($files);
                    $view->jsFiles[$pos] = array_merge($view->jsFiles[$pos], $posIgnoreFile);
                }
            }
//            $view->jsFiles = [];
//            ObbzYii::debug($view->jsFiles);
            \Yii::endProfile('Compress js files');
        }


        if ($view->js && $this->jsCompress)
        {
            \Yii::beginProfile('Compress js code');
            foreach ($view->js as $pos => $parts)
            {
                if ($parts)
                {
                    $view->js[$pos] = $this->_processingJs($parts);
                }
            }
            \Yii::endProfile('Compress js code');
        }



        if ($view->cssFiles && $this->cssFileCompile)
        {
            \Yii::beginProfile('Compress css files');

            $view->cssFiles = $this->_processingCssFiles($view->cssFiles);
            \Yii::endProfile('Compress css files');
        }


        if ($view->css && $this->cssCompress)
        {
            \Yii::beginProfile('Compress css code');

            $view->css = $this->_processingCss($view->css);

            \Yii::endProfile('Compress css code');
        }

        if ($view->css && $this->cssCompress)
        {
            \Yii::beginProfile('Compress css code');

            $view->css = $this->_processingCss($view->css);

            \Yii::endProfile('Compress css code');
        }



        if ($view->cssFiles && $this->cssFileBottom)
        {
            \Yii::beginProfile('Moving css files bottom');

            if ($this->cssFileBottomLoadOnJs)
            {
                \Yii::beginProfile('load css on js');

                $cssFilesString = implode("", $view->cssFiles);
                $view->cssFiles = [];

                $script = Html::script(new JsExpression(<<<JS
        document.write('{$cssFilesString}');
JS
                ));

                if (ArrayHelper::getValue($view->jsFiles, View::POS_END))
                {
                    $view->jsFiles[View::POS_END] = ArrayHelper::merge($view->jsFiles[View::POS_END], [$script]);

                } else
                {
                    $view->jsFiles[View::POS_END][] = $script;
                }


                \Yii::endProfile('load css on js');
            } else
            {
                if (ArrayHelper::getValue($view->jsFiles, View::POS_END))
                {
                    $view->jsFiles[View::POS_END] = ArrayHelper::merge($view->cssFiles, $view->jsFiles[View::POS_END]);

                } else
                {
                    $view->jsFiles[View::POS_END] = $view->cssFiles;
                }

                $view->cssFiles = [];
            }

            \Yii::endProfile('Moving css files bottom');
        }
    }

}