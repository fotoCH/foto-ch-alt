<?php

class FotoCache{

    const MAX_CACHE_TIME_SEC = 604800;

    private $folder;
    private $filePrefix;

    function __construct($filePrefix = ''){
        $this->folder = dirname(__FILE__) . '/icache/';
        $this->filePrefix = $filePrefix;
    }

    public function isCached($key)
    {
        if(file_exists($this->getFilePath($key))){
            $maximal_timestamp = time() - self::MAX_CACHE_TIME_SEC;
            $file_timestamp = filemtime($this->getFilePath($key));
            return $maximal_timestamp < $file_timestamp;
        }else{
            return false;
        }
    }

    public function getCache($key, $method = 'unserialize')
    {
        try{
            $fileHandler = fopen($this->getFilePath($key),"r");
            if(!$fileHandler){
                throw new Exception('File open failed.');
            }
            return $method(stream_get_contents($fileHandler));

        }catch(Exception $e){
            return false;
        }

    }

    public function cache($key, $data, $method = "serialize"){
        file_put_contents($this->getFilePath($key), $method($data));
    }

    private function getFilePath($key){
        return $this->folder . $this->getFileName($key);
    }

    private function getFileName($key){
        return $this->filePrefix . $key;
    }
}

?>