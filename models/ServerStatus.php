<?php
/**
 * @author  Mattakorn Limkool <obbz.dev@gmail.com>
 *
 */

namespace obbz\yii2\models;


use obbz\yii2\utils\ObbzYii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class ServerStatus extends Model
{
    // storage
    public $maxStorageSize = 2000000000; // 2gb
    public $currentStorageSize = 0;
    public $overMaxStorageSize = 0;
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

    public function getStroages(){
        $this->storages['database']['path'] = \Yii::$app->params['dbPath'];
        $this->storages['backup']['path'] = \Yii::$app->params['backupPath'];
        return $this->storages;
    }

    public function setStroages($storages){
        $this->storages = $storages;
    }

    public function prepareCurrentStorage($cache){

        $storages = $this->getStroages();
        foreach($storages as $key => $storage){
            $cacheKey = 'storage.' . $key . '.size';
            if($storage['cache'] && $cache && ObbzYii::cache()->get($cacheKey)){
                $storages[$key]['size'] = ObbzYii::cache()->get($cacheKey);
            }else{
                $storages[$key]['size'] = $this->allFolderSize($storage['path']);
                if($storage['cache']){
                    ObbzYii::cache()->set($cacheKey,  $storage['size'], $storage['cache']);
                }
            }

            if($storage['include']){
                $this->currentStorageSize += $storages[$key]['size'];
            }
        }

        if($this->currentStorageSize > $this->maxStorageSize){
            $this->overMaxStorageSize = $this->currentStorageSize - $this->maxStorageSize;
        }

        $this->setStroages($storages);

    }

    public function allFolderSize($dirs){
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
        if(is_file($dir) || is_dir($dir)){
            $countSize = 0;
            $count = 0;
            $dirArray = scandir($dir);
            foreach($dirArray as $key=>$filename){
                if($filename!=".." && $filename!="."){
                    if(is_dir($dir."/".$filename)){
                        $newFoldersize = $this->folderSize($dir."/".$filename);
                        $countSize = $countSize+ $newFoldersize;
                    }else if(is_file($dir."/".$filename)){
                        $countSize = $countSize + filesize($dir."/".$filename);
                        $count++;
                    }
                }
            }
            return $countSize;
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