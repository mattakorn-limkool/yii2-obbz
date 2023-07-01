<?php

/**
 *
 * basic preset returns the basic toolbar configuration set for CKEditor.
 *
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 */

//$faPath = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css';
//$faAsset = \obbz\yii2\utils\ObbzYii::app()->assetManager->getBundle(\backend\assets\CustomAsset::class);
//$faPath = $faAsset->baseUrl . '/'. $faAsset->css['fa'];
$cssPath = \obbz\yii2\utils\ObbzYii::assetBaseUrl('css/ckeditor.css', \backend\assets\CustomAsset::class);
//\obbz\yii2\utils\ObbzYii::debug($cssPath);

$conf = [
    'language' => 'en',
    'height' => 500,
    'toolbarGroups' => [
        ['name' => 'basicstyles', 'groups' => ['basicstyles', 'align', 'colors','cleanup']],
        ['name' => 'paragraph', 'groups' => ['list', 'indent', 'blocks', 'align' ]],
        ['name' => 'links',],

        ['name' => 'styles'],
        ['name' => 'blocks'],
//        '/',
        ['name' => 'insert', 'groups' => [ 'youtube' ]],
        ['name' => 'tools'],

    ],
    'removeButtons' => 'Subscript,Superscript,Flash,Smiley,SpecialChar,Strike,Iframe,Paste,PasteText,CopyFormatting,CreateDiv',
//    'removeButtons' => 'Subscript,Superscript,Flash,Smiley,SpecialChar,Strike,Iframe,Paste,PasteText,CopyFormatting',
    'removePlugins' => 'elementspath',
    'removeDialogTabs' => 'link:upload;image:advanced',
    'resize_enabled' => true,
    'extraPlugins' => 'justify, youtube, obbzmodule, btgrid',
//    'extraPlugins' => 'youtube, ckeditorfa, obbzmodule',
//    'allowedContent'=> true,
    'contentsCss' => $cssPath,

    'filebrowserUploadMethod'  => "form",


];
if(\obbz\yii2\utils\ObbzYii::user()->can(\common\components\Roles::ADMIN)){
    $conf['toolbarGroups'][] = ['name' => 'mode'];
}

return $conf;
