<?php

class FotoCache{
    private $folder;
    private $filePrefix;

    function __construct($filePrefix = ''){
        $this->folder = dirname(__FILE__) . '/icache/';
        $this->filePrefix = $filePrefix;
    }

    public function isCached($key)
    {
        return file_exists($this->getFilePath($key));
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