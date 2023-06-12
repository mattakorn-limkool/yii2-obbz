<?php
namespace obbz\yii2\components\i18n;
use obbz\yii2\admin\models\TranslateModel;
use obbz\yii2\utils\ObbzYii;
use yii\helpers\FileHelper;
use yii\helpers\VarDumper;

/**
 * @author  Mattakorn Limkool <obbz.dev@gmail.com>
 *
 */
class PhpMessageSource extends \yii\i18n\PhpMessageSource
{
    public $adminClass = TranslateModel::class;
    public $config = [
        'fileHeader' => ''
    ];

    /**
     * get all message by category
     * @param $category
     * @param $language
     * @return array
     */
    public function adminLoadMessages($category, $language){
        return $this->loadMessages($category, $language);
    }

    /**
     * write messages to file
     * @param $category
     * @param $language
     * @param $messages
     */
    public function adminSaveMessage($category, $language, $messages){
        $savePath = $this->getMessageFilePath($category, $language);
        if(is_file($savePath) && is_writable($savePath)){
            $array = VarDumper::export($messages);
            $content = <<<EOD
<?php
{$this->config['fileHeader']}
return $array;

EOD;
            return file_put_contents($savePath, $content, LOCK_EX);
        }

        return false;


//        ObbzYii::debug($messages);
    }
}