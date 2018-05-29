<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\utils\xpdf;


use obbz\yii2\utils\ObbzYii;
use yii\base\Exception;

class Xpdf
{
    public static function isWindow(){
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return true;
        } else {
            return false;
        }
    }
    public static function getCmdPath($action = ''){
        if(isset(ObbzYii::app()->params['xpdf.path'])){
            if(!empty(ObbzYii::app()->params['xpdf.path'])){
                if(self::isWindow()){
                    return ObbzYii::app()->params['xpdf.path'] . $action .'.exe';
                }else{
                    return ObbzYii::app()->params['xpdf.path'] . $action;
                }

            }
        }
        throw new Exception('Pleses set xpdfReader.path on params config');
    }

    #region INFO
    /**
     * @param $pdfPath string
     * @return PdfInfo
     * @throws Exception
     */
    public static function getInfo($pdfPath){
        if(is_file($pdfPath)){
            $action = 'pdfinfo';
            $cmd = self::getCmdPath($action) . ' "'. $pdfPath .'"';
            exec($cmd, $output);
            $info = new PdfInfo();
            $info->setAttributesByOutput($output);
            return $info;
        }else{
            throw new Exception('File Not found on server');
        }

    }


    #endregion
}