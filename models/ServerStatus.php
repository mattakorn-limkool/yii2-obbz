<?php
/**
 * @author  Mattakorn Limkool <obbz.dev@gmail.com>
 *
 */

namespace obbz\yii2\models;


use obbz\yii2\utils\ObbzYii;
use yii\base\Model;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class ServerStatus extends Model
{
    // storage
    public $maxStorageSize = 2000000000; // 2gb
    public $notifyAtRemainSize = 200000000; //200mb
    public $currentStorageSize = 0;
    public $overMaxStorageSize = 0;
    public $remainingStorageSize = 0;

    private $notifyStorage = false;
    protected $storages = [
        // files
        'vendor' => ['name'=>'Core System Files', 'path'=>"@vendor", 'size'=>0, 'show'=>true, 'include'=>true, 'cache'=>2592000 /* 30 days*/],
        'project' => ['name'=>'Project Files', 'path'=>"@app/../", 'size'=>0, 'show'=>true, 'include'=>true, 'cache'=>false],
        'upload' => ['name'=>'Upload Files', 'path'=>"@uploadPath", 'size'=>0, 'show'=>false, 'include'=>false, 'cache'=>false],

        // db
        'database' => ['name'=>'Database', 'path'=>"dbpath", 'size'=>0, 'show'=>true, 'include'=>true, 'cache'=>false],

        // backup
        'backup' => ['name'=>'Backup Files', 'path'=>"backuppath", 'size'=>0, 'show'=>true, 'include'=>true, 'cache'=>86400],
    ];

    // bandwidth
    public $maxBandwidth = 20000000000; // 20gb
    public $currentBandwidth = 0;
    public $overMaxBandwidth = 0;
    protected $accessLogConfig;

    public function prepareData($cache = true){
        $this->prepareCurrentStorage($cache);
//        $this->prepareBandwidth($cache);
    }

    public function getUsagePercent($current, $max){
        if($current >= $max){
            return 100;
        }else{
            return $current / $max * 100;
        }
    }

    public function getProgressBar($current, $max){
        $usagePercent = $this->getUsagePercent($current, $max);
        $textProgress = 'progress-bar-success';
        if($usagePercent > 99){
            $textProgress = 'progress-bar-danger';
        }else if($usagePercent > 70){
            $textProgress = 'progress-bar-warning';
        }
        return [
            'usagePercent'=>$usagePercent,
            'maxPercent'=>100,
            'textProgress'=>$textProgress,
        ];
    }

    #region Storages

    public function needNotifyStorage(){
        return $this->notifyStorage;
    }

    public function getStorages(){
        $this->storages['database']['path'] = \Yii::$app->params['dbPath'];
        $this->storages['backup']['path'] = \Yii::$app->params['backupPath'];
        return $this->storages;
    }

    public function setStorages($storages){
        $this->storages = $storages;
    }

    public function prepareCurrentStorage($cache){

        $storages = $this->getStorages();
        foreach($storages as $key => $storage){
            $cacheKey = 'storage.' . $key . '.size';
            if($storage['cache'] && $cache && ObbzYii::cache()->get($cacheKey)){
                $storages[$key]['size'] = ObbzYii::cache()->get($cacheKey);
            }else{
                $storages[$key]['size'] = $this->allStorageSize($storage['path']);
                if($storage['cache']){
                    ObbzYii::cache()->set($cacheKey,  $storage['size'], $storage['cache']);
                }
            }

            if($storage['include']){
                $this->currentStorageSize += $storages[$key]['size'];
            }
        }
        $this->remainingStorageSize =  $this->maxStorageSize - $this->currentStorageSize;
        if($this->remainingStorageSize < 0){
            $this->overMaxStorageSize = $this->currentStorageSize - $this->maxStorageSize;
        }


        if($this->remainingStorageSize <= $this->notifyAtRemainSize){
            $this->notifyStorage = true;
        }

        $this->setStorages($storages);

    }

    public function allStorageSize($dirs){
        if(is_array($dirs)){
            $size = 0;
            foreach($dirs as $dir){
                $size += $this->folderSize(\Yii::getAlias($dir));
            }
            return $size;
        }else{
            return $this->folderSize(\Yii::getAlias($dirs));
        }
    }

    public function folderSize($dir)
    {
        $isDb = substr($dir, 0, 3);
        if($isDb == 'db:'){ // calculate db size
            $db = substr($dir, 3);
            $row = \Yii::$app->getDb()->createCommand('
              SELECT table_schema "database", SUM(data_length + index_length)  "size"
              FROM information_schema.tables
              WHERE table_schema = "'. $db .'"
              GROUP BY table_schema')->queryOne();
            if(count($row) > 0){
                return $row['size'];
            }
            return 0;

        }else{ // calculate folder size
            if(is_file($dir) || is_dir($dir)){
//                $countSize = 0;
//                $count = 0;
//                $dirArray = scandir($dir);
//                foreach($dirArray as $key=>$filename){
//                    if($filename!=".." && $filename!="."){
//                        if(is_dir($dir."/".$filename)){
//                            $newFoldersize = $this->folderSize($dir."/".$filename);
//                            $countSize = $countSize+ $newFoldersize;
//                        }else if(is_file($dir."/".$filename)){
//                            $countSize = $countSize + filesize($dir."/".$filename);
//                            $count++;
//                        }
//                    }
//                }
//                return $countSize;

                $size = 0;
                // linux
                if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
                    $io = popen("du -sb " . escapeshellarg($dir), 'r');
                    if ($io) {
                        $size = fgets($io, 4096);
                        $size = explode("\t", $size)[0];
                        pclose($io);
                        if (is_numeric($size)) return (float)$size;
                    }
                }else{ // other
                    try{
                        $files = new \RecursiveIteratorIterator(
                            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
                            \RecursiveIteratorIterator::SELF_FIRST
                        );
                        foreach ($files as $file) {
                            try {
                                if ($file->isFile()) {
                                    $size += $file->getSize();
                                }
                            } catch (Exception $e) {
                                continue;
                            }
                        }
                    }catch (Exception $e) {
                        return 0;
                    }

                }
                return $size;
            }
        }

        return 0;

    }


    public function storageSizeProgressBar(){
        return $this->getProgressBar($this->currentStorageSize, $this->maxStorageSize);
    }
    #endregion

    #region Bandwidth

    public function prepareBandwidth($cache){
        $this->accessLogConfig = \Yii::$app->params['accessLogConfig'];
        $this->currentBandwidth = $this->readApacheAccessLog($this->accessLogConfig);
        if($this->currentBandwidth > $this->maxBandwidth){
            $this->overMaxBandwidth = $this->currentBandwidth - $this->maxBandwidth;
        }
    }

    public function readApacheAccessLog($conf){
        $fh = fopen($conf['path'], 'r');
        if (!$fh){
            return 0;
        }

        $totalBytes = 0;


        if(ArrayHelper::getValue($conf, 'isVhost') == true){
            $domainPort = 0;
            $bytesIndex = 8;
            $isVhost = true;
        }else{
            $domainPort = -1;
            $bytesIndex = 7;
            $isVhost = false;
        }


        while (($info = fgetcsv($fh, 0, ' ', '"')) !== false) {
            $allowed = true;

            // filter domain and port by pattern
            $domainsPattern = ArrayHelper::getValue($conf, 'domainsPattern');
            if($isVhost && !empty($domainsPattern)){
                if(preg_match($domainsPattern, $info[$domainPort])){
                    $allowed = true;
                }else{
                    $allowed = false;
                }
            }

            // todo - must be filter by date

            if($allowed){
                $totalBytes += $info[$bytesIndex];
            }

        }

        fclose($fh);
        return $totalBytes;
    }

    public function bandwidthProgressBar(){
        return $this->getProgressBar($this->currentBandwidth, $this->maxBandwidth);
    }

    #endregion
}